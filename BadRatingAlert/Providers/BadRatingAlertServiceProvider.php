<?php

namespace Modules\BadRatingAlert\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use App\Mailbox;
use App\Thread;
use App\Conversation;
use App\Customer;
use Modules\BadRatingAlert\Entities\BadRatingAlert;
class BadRatingAlertServiceProvider extends ServiceProvider
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
        //send notification on slack if rating 3 by customer
        \Eventy::addAction('satratings.feedback_sent', function($thread, $rating) {
           if($thread->rating){
                $getMailboxId=Conversation::find($thread->conversation_id);
                if($getMailboxId){
                   $mailbox = Mailbox::find($getMailboxId->mailbox_id);
                   if($mailbox){
                    $BadRatingAlert=BadRatingAlert::where('mailbox_id',$mailbox->id)->first();
                    $customer=Customer::find($thread->customer_id);
                    if($BadRatingAlert){
                        if($BadRatingAlert->enable_slack_notification=='1'){
                            if($BadRatingAlert->rating_great || $BadRatingAlert->rating_okay || $BadRatingAlert->rating_not_okay){
                                if($thread->rating=='3'){
                                   $emoji=':no_entry:';
                                }elseif($thread->rating=='1'){
                                    $emoji=':star2:';
                                }elseif($thread->rating=='2'){
                                    $emoji=':handshake:';
                                }

                                if($customer){
                                    $customerName=$customer->first_name.' '.$customer->last_name;
                                }else{
                                    $customerName='';
                                }
                                if($thread->rating_comment){
                                    $comment=$thread->rating_comment;
                                }else{
                                    $comment='No comment';
                                }
                                if($BadRatingAlert->rating_great==$thread->rating){
                                    $ratingComment="Great";
                                }elseif($BadRatingAlert->rating_okay==$thread->rating){
                                    $ratingComment="Okay";
                                }elseif($BadRatingAlert->rating_not_okay==$thread->rating){
                                    $ratingComment="Not Good";
                                }
                                $conversationUrl="<".$getMailboxId->url()."| # ".$thread->conversation_id.">";

                                $data = array(
                                    "attachments" => array(
                                        array(
                                            "color" => "#b0c4de",
                                            "fallback" => 'Support Feedback Message',
                                            "text" => $emoji." New Support Feedback \n Mailbox: ".$mailbox->name." \n Conversation :".$conversationUrl." \n Rating : ".$ratingComment." \n Comment: ".$comment." \nCustomer Name: ".$customerName,
                                        )
                                    )
                                );
                                $json_string = json_encode($data);
                                if($BadRatingAlert->slack_url){
                                    $slack_webhook_url = $BadRatingAlert->slack_url;
                                }else{
                                    $slack_webhook_url = 'https://hooks.slack.com/services/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';
                                }
                                $slack_call = curl_init($slack_webhook_url);
                                curl_setopt($slack_call, CURLOPT_CUSTOMREQUEST, "POST");
                                curl_setopt($slack_call, CURLOPT_POSTFIELDS, $json_string);
                                curl_setopt($slack_call, CURLOPT_CRLF, true);
                                curl_setopt($slack_call, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($slack_call, CURLOPT_HTTPHEADER, array(
                                    "Content-Type: application/json",
                                    "Content-Length: " . strlen($json_string))
                                );
                                $result = curl_exec($slack_call);
                                curl_close($slack_call);
                            }
                        }
                    }
                   }
                }      
           }
        }, 20, 2);
         // Add Bad Rating Alert item to the mailbox menu
        \Eventy::addAction('mailboxes.settings.menu', function($mailbox) {
            if (auth()->user()->isAdmin()) {
                echo \View::make('badratingalert::partials/settings_menu', ['mailbox' => $mailbox])->render();
            }
        }, 30);
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
            __DIR__.'/../Config/config.php' => config_path('badratingalert.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'badratingalert'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/badratingalert');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/badratingalert';
        }, \Config::get('view.paths')), [$sourcePath]), 'badratingalert');
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
