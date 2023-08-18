<?php

namespace Modules\AutoSignature\Providers;

use App\User;
use App\Conversation;
use App\Mailbox;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\AutoSignature\Entities\AutoSignature;
use Modules\AutoSignature\Entities\AutoSignatureSequence;
use Modules\AutoSignature\Entities\AutoSignatureCount;
define('AS_MODULE', 'autosignature');
use App\Thread;

class AutoSignatureServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The policy mappings for the application.
     *
     * @var array
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
        //$this->registerPolicies();
        $this->hooks();
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        // Add module's CSS file to the application layout.
        \Eventy::addFilter('stylesheets', function($styles) {
            $styles[] = \Module::getPublicPath(AS_MODULE).'/css/module.css';
            return $styles;
        });

        // Add module's JS file to the application layout.
        \Eventy::addFilter('javascripts', function($javascripts) {
            $javascripts[] = \Module::getPublicPath(AS_MODULE).'/js/laroute.js';
            if (!preg_grep("/html5sortable\.js$/", $javascripts)) {
                $javascripts[] = \Module::getPublicPath(AS_MODULE).'/js/html5sortable.js';
            }
            $javascripts[] = \Module::getPublicPath(AS_MODULE).'/js/module.js';

            return $javascripts;
        });
        
        // JS messages
        \Eventy::addAction('js.lang.messages', function() {
            ?>
                "new_saved_reply": "<?php echo __("New Auto Signature") ?>",
                "confirm_delete_saved_reply": "<?php echo __("Delete this Auto Signature?") ?>",
            <?php
        });

        // Add Saved Replies item to the mailbox menu
        \Eventy::addAction('mailboxes.settings.menu', function($mailbox) {
            $user = auth()->user();
            if ($user->isAdmin() || $user->hasPermission(User::PERM_EDIT_SAVED_REPLIES)) {
                echo \View::make('autosignature::partials/settings_menu', ['mailbox' => $mailbox])->render();
            }
        }, 20);

        // Show saved replies in reply editor
        \Eventy::addAction('reply_form.after', [$this, 'editorDropdown']);
        \Eventy::addAction('new_conversation_form.after', [$this, 'editorDropdown']);

        // Determine whether the user can view mailboxes menu.
        \Eventy::addFilter('user.can_view_mailbox_menu', function($value, $user) {
            return $value || $user->hasPermission(User::PERM_EDIT_SAVED_REPLIES);
        }, 20, 2);
        
        //set user permission
        \Eventy::addFilter('user_permissions.list', function($list) {
            $list[] = AutoSignature::PERM_EDIT_AUTOSIGNATURE;
            return $list;
        });

        \Eventy::addFilter('user_permissions.name', function($name, $permission) {
            if ($permission != AutoSignature::PERM_EDIT_AUTOSIGNATURE) {
                return $name;
            }
            return __('Users are allowed to manage auto signature');
        }, 20, 2); 

        // Redirect user to the accessible mailbox settings route.
        \Eventy::addFilter('mailbox.accessible_settings_route', function($value, $user, $mailbox) {
            if ($user->hasPermission(User::PERM_EDIT_SAVED_REPLIES) && $mailbox->userHasAccess($user->id)) {
                return 'mailboxes.auto_signature';
            } else {
                return $value;
            }
        }, 20, 3);
        // Add auto signature in email to customer
        \Eventy::addAction('reply_email.after_signature', function($thread, $loop, $threads, $conversation, $mailbox) {
              if($mailbox->auto_signature && $loop->first && $thread->source_via == Thread::PERSON_USER){
                   echo $mailbox->auto_signature; 
              }
        }, 9, 5);
        // Show on conversation creation
        \Eventy::addAction('conversation.create_form.after_subject', function($conversation, $mailbox) {

           $checkAutoSignature = AutoSignature::where('mailbox_id',$mailbox->id)->count();
           if($checkAutoSignature){

                $autosignature          = AutoSignature::where('mailbox_id',$mailbox->id)->first();
                if($autosignature->id){
                   $checkAutoSignatureCount= AutoSignatureCount::where('auto_signature_id',$autosignature->id)->first();
                    if($checkAutoSignatureCount){
                        AutoSignatureCount::where('auto_signature_id',$autosignature->id)->update(['auto_signature_id'=>$autosignature->id,'count'=>$checkAutoSignatureCount->count+1]);
                    }else{
                        $autoSignatureCount     = new AutoSignatureCount();
                        $autoSignatureCount->auto_signature_id = $autosignature->id;
                        $autoSignatureCount->count=1;
                        $autoSignatureCount->save();
                    }  
                }
                $AutoSignatureSequence  = new AutoSignatureSequence();
                $AutoSignatureSequence->auto_signature_id=$autosignature->id;
                $AutoSignatureSequence->mailbox_id=$mailbox->id;
                $AutoSignatureSequence->save();
                
                $insertAutoSignature=Mailbox::find($mailbox->id);
                $insertAutoSignature->auto_signature=$autosignature->text;
                $insertAutoSignature->save();
                //echo \View::make('autosignature::partials/autosignature', [
                    //'autosignature' => $autosignature
               // ])->render();
                if (!$autosignature) {
                    return;
                }
            }else{
                return;
            }
        }, 20, 2);
        // Show the auto signature in reply conversation
        \Eventy::addAction('conversation.after_subject_block', function($conversation, $mailbox) {
                $checkAutoSignature = AutoSignature::where('mailbox_id',$mailbox->id)->count();
               if($checkAutoSignature){
                    $autoSignatureSequenceCheck=AutoSignatureSequence::where(['conversation_id'=>null,'mailbox_id'=>$mailbox->id])->count();

                    if($autoSignatureSequenceCheck){
                        $autoSignatureSequence=AutoSignatureSequence::where(['conversation_id'=>null,'mailbox_id'=>$mailbox->id])->first();
                     
                        $autosignature          = AutoSignature::where('id',$autoSignatureSequence->auto_signature_id)->first();

                        if($autosignature->id){
                            $autoSignatureSequenceCheck=AutoSignatureSequence::where(['auto_signature_id'=>$autosignature->id,'conversation_id'=>$conversation->id,'mailbox_id'=>$mailbox->id])->count();
                            if($autoSignatureSequenceCheck){

                                $AutoSignatureSequenceUpdateAfter  =AutoSignatureSequence::where(['auto_signature_id'=>$autosignature->id,'mailbox_id'=>$autosignature->mailbox_id])->first();
                               
                                if($AutoSignatureSequenceUpdateAfter){
                                    $autoIncrement=$autosignature->id+1;
                                    $AutoSignatureSequenceUpdateAfter->auto_signature_id=$autoIncrement;
                                    $AutoSignatureSequenceUpdateAfter->mailbox_id = $mailbox->id;
                                    $AutoSignatureSequenceUpdateAfter->conversation_id = $conversation->id;
                                    $AutoSignatureSequenceUpdateAfter->save();
                                }
                                
                            }else{
                                $updateData=['auto_signature_id'=>$autosignature->id,'mailbox_id'=>$mailbox->id,'conversation_id'=>$conversation->id];
                                $AutoSignatureSequenceAfterNew  = AutoSignatureSequence::where(['conversation_id'=>null,'auto_signature_id'=>$autosignature->id,'mailbox_id'=>$mailbox->id])->update($updateData);
                            }
                        }
                    }else{
                        $autoSignatureSequenceConversationCheck=AutoSignatureSequence::where(['conversation_id'=>$conversation->id,'mailbox_id'=>$mailbox->id])->count();
                        if($autoSignatureSequenceConversationCheck){
                            $autoSignatureSequenceConversation=AutoSignatureSequence::where(['conversation_id'=>$conversation->id,'mailbox_id'=>$mailbox->id])->first();
                            $autosignature          = AutoSignature::where('id',$autoSignatureSequenceConversation->auto_signature_id)->first();
                            if($autosignature){
                                $updateAutoSignatureSequence  =AutoSignatureSequence::find($autoSignatureSequenceConversation->id);
                                $updateAutoSignatureSequence->auto_signature_id=$autoSignatureSequenceConversation->auto_signature_id+1;
                                $updateAutoSignatureSequence->mailbox_id = $mailbox->id;
                                $updateAutoSignatureSequence->conversation_id = $autoSignatureSequenceConversation->conversation_id;
                                $updateAutoSignatureSequence->save();
                            }else{
                                $autosignature          = AutoSignature::where(['mailbox_id'=>$mailbox->id])->first();
                                $updateAutoSignatureSequence  =AutoSignatureSequence::find($autoSignatureSequenceConversation->id);
                                $updateAutoSignatureSequence->auto_signature_id=$autosignature->id;
                                $updateAutoSignatureSequence->mailbox_id = $mailbox->id;
                                $updateAutoSignatureSequence->conversation_id = $autoSignatureSequenceConversation->conversation_id;
                                $updateAutoSignatureSequence->save();
                            }

                        }else{
                            $autosignature          = AutoSignature::where('mailbox_id',$mailbox->id)->first();
                            $AutoSignatureSequenceNew  = new AutoSignatureSequence();
                            $AutoSignatureSequenceNew->auto_signature_id=$autosignature->id;
                            $AutoSignatureSequenceNew->mailbox_id=$mailbox->id;
                            $AutoSignatureSequenceNew->conversation_id = $conversation->id;
                            $AutoSignatureSequenceNew->save();
                        }
                    }

                    if (!$autosignature) {
                        return;
                    }
                    if($autosignature->id){
                       $checkAutoSignatureCount= AutoSignatureCount::where('auto_signature_id',$autosignature->id)->first();
                       if($checkAutoSignatureCount){
                            AutoSignatureCount::where('auto_signature_id',$autosignature->id)->update(['auto_signature_id'=>$autosignature->id,'count'=>$checkAutoSignatureCount->count+1]);
                       }else{
                            $autoSignatureCount     = new AutoSignatureCount();
                            $autoSignatureCount->auto_signature_id = $autosignature->id;
                            $autoSignatureCount->count=1;
                            $autoSignatureCount->save();
                       }
                        
                    }
                    if($autosignature){
                        $insertAutoSignature=Mailbox::find($mailbox->id);
                        $insertAutoSignature->auto_signature=$autosignature->text;
                        $insertAutoSignature->save();
                    //echo \View::make('autosignature::partials/autosignature', [
                        //'autosignature' => $autosignature
                   // ])->render();
                        
                    }
                   
                }else{
                        return;
                }

        }, 30, 2);

        // Select main menu item.
        \Eventy::addFilter('menu.selected', function($menu) {
            $menu['manage']['mailboxes'][] = 'mailboxes.auto_signature';

            return $menu;
        });
    }

    /**
     * Show saved replies in reply editor
     * @param  [type] $conversation [description]
     * @return [type]               [description]
     */
    public function editorDropdown($conversation)
    {
        $saved_replies = AutoSignature::where('mailbox_id', $conversation->mailbox->id)
            ->select(['id', 'name'])
            ->orderby('sort_order')
            ->get();
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
            __DIR__.'/../Config/config.php' => config_path('autosignature.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'autosignature'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/autosignature');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/autosignature';
        }, \Config::get('view.paths')), [$sourcePath]), 'autosignature');
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
