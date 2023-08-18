<?php

namespace Modules\EasyDigitalDownload\Providers;
use Carbon\Carbon;
use App\Mailbox;
use Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

define('MODULE_EDD', 'easydigitaldownload');

class EasyDigitalDownloadServiceProvider extends ServiceProvider
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
                echo \View::make('easydigitaldownload::partials/settings_menu', ['mailbox' => $mailbox])->render();
            }
        }, 30);

        // Add module's JS file to the application layout.
        \Eventy::addFilter('javascripts', function($value) {
           
            array_push($value, '/modules/'.MODULE_EDD.'/js/module.js');
            return $value;
        });
        
        // Show recent orders.
        \Eventy::addAction('conversation.after_prev_convs', function($customer, $conversation, $mailbox) {
            $load = false;
            $orders = [];

            $customer_email = $customer->getMainEmail();
            if (!$customer_email) {
                return;
            }
            Session::put('mailbox', ['eddurls' => $mailbox->eddurls, 'eddkey' => $mailbox->eddkey, 'eddtoken' => $mailbox->eddtoken]);

            $cached_orders = [];
            $cached_orders_json = \Cache::get('wc_orders_'.$customer_email);

            if ($cached_orders_json && is_array($cached_orders_json)) {
                $orders = $cached_orders_json;
            } else {
                $load = true;
            }
            if(!empty($mailbox->eddurls) || !empty($mailbox->eddtoken) || !empty($mailbox->eddkey)){
                echo \View::make('easydigitaldownload::partials/orders', [
                    'orders'         => $orders,
                    'customer_email' => $customer_email,
                    'load'           => $load,
                    'url'            => $this->getSanitizedUrl($mailbox->eddurls),
                ])->render();
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
            __DIR__.'/../Config/config.php' => config_path('easydigitaldownload.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'easydigitaldownload'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
   public function registerViews()
    {
        $viewPath = resource_path('views/modules/easydigitaldownload');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/easydigitaldownload';
        }, \Config::get('view.paths')), [$sourcePath]), 'easydigitaldownload');
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
    public static function getSanitizedUrl($eddurl)
    {

        $eddurl = preg_replace("/https?:\/\//i", '', $eddurl);

        if (substr($eddurl, -1) != '/') {
            $eddurl .= '/';
        }

        return 'https://'.$eddurl;
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
