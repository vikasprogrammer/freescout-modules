<?php

namespace Modules\EnvatoIntegration\Providers;

use App\Conversation;
use Carbon\Carbon;
use Modules\EnvatoIntegration\Entities\EnvatoCustomField;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

define('ECF_MODULE', 'envatointegration');

class EnvatoCustomFieldsServiceProvider extends ServiceProvider
{
    public static $search_custom_fields = [];

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

    public function hooks()
    {
        // Add module's CSS file to the application layout.
        \Eventy::addFilter('stylesheets', function($styles) {
            $styles[] = \Module::getPublicPath(ECF_MODULE).'/css/module.css';
            return $styles;
        });

        // Add module's JS file to the application layout.
        \Eventy::addFilter('javascripts', function($javascripts) {
            $javascripts[] = \Module::getPublicPath(ECF_MODULE).'/js/laroute.js';
            if (!preg_grep("/html5sortable\.js$/", $javascripts)) {
                $javascripts[] = \Module::getPublicPath(ECF_MODULE).'/js/html5sortable.js';
            }
            $javascripts[] = \Module::getPublicPath(ECF_MODULE).'/js/module.js';

            return $javascripts;
        });

        // JavaScript in the bottom
        \Eventy::addAction('javascript', function() {
            if (\Route::is('conversations.view') || \Route::is('conversations.create')) {
                echo 'initEnvato();';
            }
        });

        // JS messages
        \Eventy::addAction('js.lang.messages', function() {
            ?>
                "confirm_delete_custom_field": "<?php echo __("Deleting this custom field will remove all historical data and deactivate related workflows. Delete this custom field?") ?>",
                "confirm_delete_cf_option": "<?php echo __("Deleting this dropdown option will remove all historical data and deactivate related workflows. Delete this dropdown option?") ?>",
            <?php
        });

        // Add item to the mailbox menu
        \Eventy::addAction('mailboxes.settings.menu', function($mailbox) {
            if (auth()->user()->isAdmin()) {
                echo \View::make('envatointegration::partials/settings_menu', ['mailbox' => $mailbox])->render();
            }
        }, 15);
        // Show recent orders.
        \Eventy::addAction('conversation.after_prev_convs', function($customer, $conversation, $mailbox) {
            $customer_email = $customer->getMainEmail();
            $cached_orders_json = \Cache::get('envato_orders'.$customer_email);
            
            if($cached_orders_json==null || empty($cached_orders_json)){
               
            }else{
                if ($cached_orders_json && is_array($cached_orders_json)) {
                    $result = $cached_orders_json;
                }
               
            } 

        }, 12, 3);
        

        // Display search filters.
        \Eventy::addAction('search.display_filters', function($filters) {
            $custom_fields = $this->getSearchCustomFields();

            if (count($custom_fields)) {
                echo \View::make('envatointegration::partials/search_filters', [
                    'custom_fields' => $custom_fields,
                    'filters'       => $filters,
                ])->render();
            }
        });

        // Search filters apply.
        \Eventy::addFilter('search.conversations.apply_filters', function($query_conversations, $filters, $q) {
            $custom_fields = $this->getSearchCustomFields();

            if (count($custom_fields)) {
                foreach ($custom_fields as $custom_field) {
                    if (!empty($filters[$custom_field->name])) {
                        $join_alias = 'ccf'.$custom_field->id;
                        $query_conversations->join('conversation__envato_custom_field as '.$join_alias, function ($join) use ($custom_field, $filters, $join_alias) {
                            $join->on('conversations.id', '=', $join_alias.'.conversation_id');
                            $join->where($join_alias.'.custom_field_id', $custom_field->id);
                            if ($custom_field->type == EnvatoCustomField::TYPE_MULTI_LINE) {
                                $join->where($join_alias.'.value', 'like', '%'.$filters[$custom_field->name].'%');
                            } else {
                                $join->where($join_alias.'.value', $filters[$custom_field->name]);
                            }
                        });
                    }
                }
            }

            return $query_conversations;
        }, 20, 3);

       
    }

    public function getSearchCustomFields()
    {
        if (self::$search_custom_fields) {
            return self::$search_custom_fields;
        }
        $mailbox_ids = auth()->user()->mailboxesIdsCanView();

        if ($mailbox_ids) {
            $custom_fields = EnvatoCustomField::whereIn('mailbox_id', $mailbox_ids)
                
                ->distinct('name')
                ->get();
    
            if (count($custom_fields)) {

                foreach ($custom_fields as $i => $custom_field) {
                    $custom_fields[$i]->name = '#'.$custom_field->name;
                }
                self::$search_custom_fields = $custom_fields;
                return $custom_fields;
            }
        }

        return [];
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
            __DIR__.'/../Config/config.php' => config_path('envatointegration.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'envatointegration'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/envatointegration');

        $sourcePath = __DIR__ . '/../Resources/view';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/envatointegration';
        }, \Config::get('view.paths')), [$sourcePath]), 'envatointegration');
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
     *
     * @return void
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
