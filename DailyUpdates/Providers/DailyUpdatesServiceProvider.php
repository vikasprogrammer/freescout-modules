<?php

namespace Modules\DailyUpdates\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class DailyUpdatesServiceProvider extends ServiceProvider
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
        $this->registerCommands();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
         // Add Power pack item to the mailbox menu
        
        \Eventy::addAction('menu.manage.after_mailboxes', function($mailbox) {
            echo \View::make('dailyupdates::partials/settings_menu', [])->render();
        });

        // Schedule background processing
        \Eventy::addFilter('schedule', function($schedule) {
            $schedule->command('freescout:daily-updates')->dailyAt('18:30');
            $schedule->command('freescout:busy-ticket-notification')->dailyAt('19:00');
            $schedule->command('freescout:customer-waiting-tickets')->cron('0 */4 * * *'); // every 4 hours
            return $schedule;
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
            __DIR__.'/../Config/config.php' => config_path('dailyupdates.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'dailyupdates'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/dailyupdates');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/dailyupdates';
        }, \Config::get('view.paths')), [$sourcePath]), 'dailyupdates');
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
    public function registerCommands()
    {
        $this->commands([
            \Modules\DailyUpdates\Console\DailyUpdate::class,
            \Modules\DailyUpdates\Console\CustomerWaitingTickets::class,
            \Modules\DailyUpdates\Console\BusyTicketNotification::class
        ]);
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
