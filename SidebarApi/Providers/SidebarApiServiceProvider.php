<?php

namespace Modules\SidebarApi\Providers;
use Carbon\Carbon;
use App\Mailbox;
use Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\SidebarApi\Entities\SidebarApi;

define('MODULE_SA', 'sidebarapi');

class SidebarApiServiceProvider extends ServiceProvider
{   

     const MAX_ORDERS = 5;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    /**

   
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTranslations();
    }


    /**
     * Module hooks.
     */
    public function hooks()
    {

        // Add Sat. Ratings item to the mailbox menu
        \Eventy::addAction('mailboxes.settings.menu', function($mailbox) {
            if (auth()->user()->isAdmin()) {
                echo \View::make('sidebarapi::partials/settings_menu', ['mailbox' => $mailbox])->render();
            }
        }, 30);

        // Add module's JS file to the application layout.
        \Eventy::addFilter('javascripts', function($value) {
           
            array_push($value, '/modules/'.MODULE_SA.'/js/module.js');
            return $value;
        });
        
        // Show recent orders.
        \Eventy::addAction('conversation.after_prev_convs', function($customer, $conversation, $mailbox) {
            $load = false;
            $customer_email = $customer->getMainEmail();
            if (!$customer_email) {
                return;
            }
            $SidebarApiUrl=SidebarApi::where('mailbox_id',$mailbox->id)->first();
            if(isset($SidebarApiUrl) && isset($customer_email)){
  
                $ApiUrl=$this->getSanitizedUrl($SidebarApiUrl->url);

                $curl = curl_init();
                $filedData = array(
                        "email"=>$customer_email
                    );
                $filedDatajson = json_encode($filedData);
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $ApiUrl,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $filedDatajson,
                  CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",
                  ),
                ));
                $result = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                $result_response = json_decode($result, true);
                if(isset($result_response['errors'])){
                    return; 
                }
                if(isset($result) ){
                    echo \View::make('sidebarapi::partials/orders', [
                    'orders'         => $result_response,
                    'customer_email' => $customer_email,
                    'load'           => $load,
                    'url'            => $ApiUrl,
                ])->render();
                }else{
                    return;
               }
            }else{
                 return;
            }

        }, 12, 3);
                // Custom menu in conversation.
        \Eventy::addAction('conversation.customer.menu', function($customer, $conversation) {
            ?>
                <li role="presentation" class="col3-hidden"><a data-toggle="collapse" href=".wc-collapse-orders" tabindex="-1" role="menuitem"><?php echo __("Recent Orders") ?></a></li>
            <?php
        }, 12, 2);


    }
    
    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('sidebarapi.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'sidebarapi'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
   public function registerViews()
    {
        $viewPath = resource_path('views/modules/sidebarapi');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/sidebarapi';
        }, \Config::get('view.paths')), [$sourcePath]), 'sidebarapi');
    }
    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__ .'/../Resources/lang');
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    public static function formatDate($date)
    {
        $date_carbon = Carbon::parse($date);

        if (!$date_carbon) {
            return '';
        }

        return $date_carbon->format('M j, Y');
    }
    public static function getSanitizedUrl($url)
    {

        $url = preg_replace("/https?:\/\//i", '', $url);

        if (substr($url, -1) != '/') {
            $url .= '';
        }

        return 'https://'.$url;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

}
