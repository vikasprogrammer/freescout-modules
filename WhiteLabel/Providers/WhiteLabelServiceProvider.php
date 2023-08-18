<?php

namespace Modules\WhiteLabel\Providers;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
define('WL_MODULE', 'whitelabel');
use App\Mailbox;
use App\User;
use Auth;
use App\Customer;
use App\Thread;
use Carbon\Carbon;
use Modules\WhiteLabel\Entities\WhiteLabel;
class WhiteLabelServiceProvider extends ServiceProvider
{
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
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        // Add White Label item to the mailbox menu
        \Eventy::addAction('mailboxes.settings.menu', function($mailbox) {
            if (auth()->user()->isAdmin()) {
                echo \View::make('whitelabel::partials/settings_menu',['mailbox' => $mailbox])->render();
            }
        }, 30);
        \Eventy::addAction('layout.body_bottom', function() {
            $url = \Request::getRequestUri();
            if(Auth::check()){
                $LoginUserEmail = Auth::user()->email;
                $whiteLabel=WhiteLabel::first();
                if($LoginUserEmail && isset($whiteLabel)){
                    if($LoginUserEmail!=$whiteLabel->user_email){
                        echo "<style>#app-navbar-collapse > ul:nth-child(1) > li.dropdown.open > ul > li:nth-child(8) > a{display:none;}</style>";
                       
                    }
                }  
            }
            $whiteLabel=WhiteLabel::first();
            if($whiteLabel){
                if($whiteLabel->copyrightText){
                    $copyright=$whiteLabel->copyrightText;
                }else{
                    $copyright=' ';
                }
                if($whiteLabel->logo){
                    $logo=$whiteLabel->logo;
                }else{
                    $logo=' ';
                }
                if($whiteLabel->brand_text){
                    $brandText=$whiteLabel->brand_text;
                }else{
                    $brandText=' ';
                }
              
            }else{
                $brandText=' ';
                $copyright=' ';
                $logo=' ';
            }
            $logo=asset('img/'.$logo);
            echo "<script>var copyright=\"$copyright\"</script>";
            echo "<script>var brandText=\"$brandText\"</script>";
            echo "<script>var logo=\"$logo\"</script>";
        });
        // Add module's JS file to the application layout.
        \Eventy::addFilter('javascripts', function($javascripts) {
            $javascripts[] = \Module::getPublicPath(WL_MODULE).'/js/laroute.js';
            $javascripts[] = \Module::getPublicPath(WL_MODULE).'/js/module.js';
            return $javascripts;
        });
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
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('whitelabel.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'whitelabel'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/whitelabel');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/whitelabel';
        }, \Config::get('view.paths')), [$sourcePath]), 'whitelabel');
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
