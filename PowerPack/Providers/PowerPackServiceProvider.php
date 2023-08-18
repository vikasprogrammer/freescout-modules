<?php

namespace Modules\PowerPack\Providers;
// Module alias.
define('PP_MODULE', 'powerpack');
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\PowerPack\Entities\PowerPack;
use Modules\PowerPack\Entities\KbCategory;
use Illuminate\Http\Request;
use App\Mailbox;
use App\Attachment;
use App\Conversation;
use App\Customer;
use App\Thread;
use App\Events\ConversationCustomerChanged;
use App\Events\CustomerCreatedConversation;
use Modules\CustomFields\Entities\ConversationCustomField;
use Illuminate\Support\Facades\Schema;
use DB;
use Modules\PowerPack\Jobs\ChatAutoReply;
use Carbon\Carbon;
class PowerPackServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    public static $mailboxes_ids = [];
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
        $checkPowerPack=PowerPack::where('mailbox_id','1')->first();
       
        \Eventy::addFilter('conversation.created_by_customer', function($conversation, $thread, $customer) {
            
            $checkPowerPack=PowerPack::where('mailbox_id',$conversation->mailbox_id)->first();
            if($checkPowerPack){
                if($checkPowerPack->minutes || $checkPowerPack->second){
                    $minutes = isset($checkPowerPack->minutes) ? $checkPowerPack->minutes : 0;
                    $secs    = isset($checkPowerPack->second) ? $checkPowerPack->second : 0;
                    $totalSecs   = ($minutes * 60) + $secs; 

                    ChatAutoReply::dispatch($conversation,$thread)->delay(Carbon::now()->AddSeconds($totalSecs));
                    //ChatAutoReply::dispatch($conversation,$thread)->delay(Carbon::now()->AddSeconds(10));
                }
                //ChatAutoReply::dispatch($conversation,$thread)->delay(Carbon::now()->addMinutes(4));
                
            }
            
            $inputData = \Request::all();
            foreach($inputData as $key=>$input) {
                if (strpos($key, 'pp_custom_field_') !== false) {
                    $field_id = str_replace("pp_custom_field_", "",$key);
                    if($field_id && $input){
                        $powerPackConversation = new ConversationCustomField();
                        $powerPackConversation->conversation_id=$conversation->id;
                        $powerPackConversation->custom_field_id=$field_id;
                        $powerPackConversation->value=$input;
                        $powerPackConversation->save();
                    }
                }
               
            }
            return $conversation;

        },20,3);
        \Eventy::addAction('conversation.user_replied', function($conversation, $thread) {
          
        }, 20, 2);

        \Eventy::addAction('conversation.customer_replied', function($conversation, $thread, $customer) {
            $checkPowerPack=PowerPack::where('mailbox_id',$conversation->mailbox_id)->first();
            if($checkPowerPack){
                if($checkPowerPack->minutes || $checkPowerPack->second){
                    $minutes = isset($checkPowerPack->minutes) ? $checkPowerPack->minutes : 0;
                    $secs    = isset($checkPowerPack->second) ? $checkPowerPack->second : 0;
                    $totalSecs   = ($minutes * 60) + $secs; 

                    ChatAutoReply::dispatch($conversation,$thread)->delay(Carbon::now()->AddSeconds($totalSecs));
                    //ChatAutoReply::dispatch($conversation,$thread)->delay(Carbon::now()->AddSeconds(10));
                }
               // ChatAutoReply::dispatch($conversation,$thread)->delay(Carbon::now()->addMinutes(4));
            }
           
        }, 20, 3);
        // Add Power pack item to the mailbox menu
        \Eventy::addAction('mailboxes.settings.menu', function($mailbox) {
            if (auth()->user()->isAdmin()) {
                echo \View::make('powerpack::partials/settings_menu', ['mailbox' => $mailbox])->render();
            }
        }, 30);

        $linkByMailboxId = \Request::url();
        $link_array = explode('/',$linkByMailboxId);
        $mailbox_ids = end($link_array);
        $mailbox = $this->processMailboxId($mailbox_ids);

        if($mailbox){
            $powerPackExit=PowerPack::where('mailbox_id',$mailbox->id)->first();
            if($powerPackExit){
                $link = \Request::url();
                $split = explode("/", $link);
                $count = count($split);
                if($count > 1){
                   if($split[$count - 2]=='help'){
                    \Eventy::addAction('layout.body_bottom', function() {
                        $link = \Request::url();
                        $link_array = explode('/',$link);
                        $mailbox_ids = end($link_array); 
                        $mailbox = $this->processMailboxId($mailbox_ids);
                        $enablePowerPackHelp=PowerPack::where('mailbox_id',$mailbox->id)->first();
                            echo "<style>.navbar-default { background-color: $enablePowerPackHelp->nav_bg_for_user_end_portal;}.navbar-default .navbar-nav > .active > a{background-color:$enablePowerPackHelp->active_menu_item_bg_user_end_portal}.navbar-default .navbar-nav > li > a:hover {background-color: $enablePowerPackHelp->active_menu_item_bg_user_end_portal}.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus {background-color: $enablePowerPackHelp->active_menu_item_bg_user_end_portal;}#eup-submit-form-bottom > div > input,#app > div.content > div > div > div > div > form > div:nth-child(3) > button{background-color:$enablePowerPackHelp->end_btn_bg_color;color:$enablePowerPackHelp->end_text_color;border:$enablePowerPackHelp->end_btn_bg_color;}$enablePowerPackHelp->add_css_user_end_portal</style>";
                    }); 
                   }else if($split[$count - 2]=='hc'){
                        \Eventy::addAction('layout.body_bottom', function() {
                            $link = \Request::url();
                            $link_array = explode('/',$link);
                            $mailbox_ids = end($link_array); 
                            $mailbox = $this->processMailboxId($mailbox_ids);
                            $enablePowerPackHp=PowerPack::where('mailbox_id',$mailbox->id)->first();
                            echo "<style>.navbar-default { background-color: $enablePowerPackHp->nav_bg_for_kb_portal;}.navbar-default .navbar-nav > .active > a{background-color:$enablePowerPackHp->active_menu_item_bg_kb_portal}.navbar-default .navbar-nav > li > a:hover {background-color: $enablePowerPackHp->active_menu_item_bg_kb_portal}.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus {background-color: $enablePowerPackHp->active_menu_item_bg_kb_portal;}$enablePowerPackHp->add_css_kb_portal</style>";
                        }); 
                   }
                }  
            }
        }else{
            $link  = \Request::url();
            $split = explode("/", $link);
            $count = count($split);
            if($count > 1){
               $mailbox = $this->processMailboxId($split[$count - 2]); 
               if($mailbox){
                    $powerPackExit=PowerPack::where('mailbox_id',$mailbox->id)->first();
                    if($powerPackExit){
                        $link = \Request::url();
                        $split = explode("/", $link);
                        $count = count($split);
                        if($count > 1){
                           if($split[$count - 3]=='help'){
                            \Eventy::addAction('layout.body_bottom', function() {
                                $linkHelp = \Request::url();
                                $splitHelp = explode("/", $linkHelp); 
                                $countHelp = count($splitHelp);
                                $mailboxHelpId = $this->processMailboxId($splitHelp[$countHelp - 2]);
                                $enablePowerPackHelp=PowerPack::where('mailbox_id',$mailboxHelpId->id)->first();
                                    echo "<style>.navbar-default { background-color: $enablePowerPackHelp->nav_bg_for_user_end_portal;}.navbar-default .navbar-nav > .active > a{background-color:$enablePowerPackHelp->active_menu_item_bg_user_end_portal}.navbar-default .navbar-nav > li > a:hover {background-color: $enablePowerPackHelp->active_menu_item_bg_user_end_portal}.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus {background-color: $enablePowerPackHelp->active_menu_item_bg_user_end_portal;}#eup-submit-form-bottom > div > input,#app > div.content > div > div > div > div > form > div:nth-child(3) > button{background-color:$enablePowerPackHelp->end_btn_bg_color;color:$enablePowerPackHelp->end_text_color;border:$enablePowerPackHelp->end_btn_bg_color;}$enablePowerPackHelp->add_css_user_end_portal</style>";
                            }); 
                           }else{
                            $link = \Request::url();
                            $split = explode("/", $link);
                            $count = count($split);
                            if($count>0){
                                $mailboxId = $this->processMailboxId($split[$count - 2]);
                                \Eventy::addAction('layout.body_bottom', function() {
                                    $link = \Request::url();
                                    $split = explode("/", $link);
                                    $count = count($split);
                                    $mailboxId = $this->processMailboxId($split[$count - 2]);
                                    $enablePowerPackHp=PowerPack::where('mailbox_id',$mailboxId->id)->first();
                                    echo "<style>.navbar-default { background-color: $enablePowerPackHp->nav_bg_for_kb_portal;}.navbar-default .navbar-nav > .active > a{background-color:$enablePowerPackHp->active_menu_item_bg_kb_portal}.navbar-default .navbar-nav > li > a:hover {background-color: $enablePowerPackHp->active_menu_item_bg_kb_portal}.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus {background-color: $enablePowerPackHp->active_menu_item_bg_kb_portal;}$enablePowerPackHp->add_css_kb_portal</style>";
                                });
                                
                            }
                           }
                        }  
                    } 
                }else{
                    $link = \Request::url();
                    $split = explode("hc/", $link);
                    $count = count($split);
                    if($count > 1){
                      $mailboxId=explode('/',$split[1]);
                        if($mailboxId[0]){
                            \Eventy::addAction('layout.body_bottom', function() {
                                $linkArticle = \Request::url();
                                $splitArticle = explode("hc/", $linkArticle);
                                $mailboxIdArticle=explode('/',$splitArticle[1]);
                                $mailbox = $this->processMailboxId($mailboxIdArticle[0]);
                                if($mailbox){
                                    $enablePowerPackHp=PowerPack::where('mailbox_id',$mailbox->id)->first();
                                    if($enablePowerPackHp){
                                        echo "<style>.navbar-default { background-color: $enablePowerPackHp->nav_bg_for_kb_portal;}.navbar-default .navbar-nav > .active > a{background-color:$enablePowerPackHp->active_menu_item_bg_kb_portal}.navbar-default .navbar-nav > li > a:hover {background-color: $enablePowerPackHp->active_menu_item_bg_kb_portal}.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus {background-color: $enablePowerPackHp->active_menu_item_bg_kb_portal;}$enablePowerPackHp->add_css_kb_portal</style>";
                                    }

                                }
                               
                            }); 
                        }
                    }
               }
            }
        }
        
        \Eventy::addAction('layout.body_bottom', function() {

            $linkByMailboxId = \Request::url();
            $link_array = explode('/',$linkByMailboxId);
            $link = \Request::url();
            $split = explode("/", $link);
            $count = count($split);
            $mailbox_ids = end($link_array);
            $mailbox = $this->processMailboxId($mailbox_ids);
            if($mailbox){
                $checkPowerPack=PowerPack::where('mailbox_id',$mailbox->id)->first();
                if($checkPowerPack){
                    if($count > 1){
                        if($split[$count - 2]=='hc'){
                            if($checkPowerPack->kbLogoImage && $checkPowerPack->kb_enable_text_logo==1 && $checkPowerPack->kbLogoText){
                                $kbLogoImage=asset('img/'.$checkPowerPack->kbLogoImage);
                                $kbLogoText=$checkPowerPack->kbLogoText;
                                $logoImageKb="<img src='".$kbLogoImage."' height='100%'><span style='padding-left:5px;'>".$kbLogoText."</span>";
                            }elseif($checkPowerPack->kbLogoImage){
                                $kbLogoImage=asset('img/'.$checkPowerPack->kbLogoImage);
                                $logoImageKb="<img src='".$kbLogoImage."' height='100%'>";
                            }elseif($checkPowerPack->enable_text_logo==1 && $checkPowerPack->kbLogoText){
                                $kbLogoText=$checkPowerPack->kbLogoText;
                                $logoImageKb="<span>".$kbLogoText."</span>";
                            }else{
                                 $logoImageKb='';
                            }
                       }else{
                            $logoImageKb='';
                       }

                        echo "<script>var logoImageKb=\"$logoImageKb\"</script>";
                    }
                    if($checkPowerPack->custom_html==1){
                        $customHtml="<h5>".$checkPowerPack->textarea_field_text."</h5>";
                    }else{
                        $customHtml='';
                    }
                    if($checkPowerPack->eupLogoImage && $checkPowerPack->enable_text_logo==1 && $checkPowerPack->eupLogoText){
                        $eupLogoImage=asset('img/'.$checkPowerPack->eupLogoImage);
                        $eupLogoText=$checkPowerPack->eupLogoText;
                        $logoImageEup="<img src='".$eupLogoImage."' height='100%'><span style='padding-left:5px;'>".$eupLogoText."</span>";
                    }elseif($checkPowerPack->eupLogoImage){
                        $eupLogoImage=asset('img/'.$checkPowerPack->eupLogoImage);
                        $logoImageEup="<img src='".$eupLogoImage."' height='100%'>";
                    }elseif($checkPowerPack->enable_text_logo==1 && $checkPowerPack->eupLogoText){
                        $eupLogoText=$checkPowerPack->eupLogoText;
                        $logoImageEup="<span>".$eupLogoText."</span>";
                    }else{
                         $logoImageEup='';
                    }
                    if($checkPowerPack->enable_kb_section==1){
                        $enable_kb_section=1;
                    }else{
                        $enable_kb_section=0;
                    }
                    if($mailbox){
                        $hcId=crc32(config('app.key').'enduserportal'.$mailbox->id);
                    }else{
                        $hcId='';
                    }
                    if($checkPowerPack->number_of_category_kb){
                        $kbCategryLimit=$checkPowerPack->number_of_category_kb;
                    }else{
                        $kbCategryLimit=5;
                    }
                    if($checkPowerPack->number_of_article_kb){
                        $kbArticleLimit=$checkPowerPack->number_of_article_kb;
                    }else{
                        $kbArticleLimit=5;
                    }
                    if(Schema::hasTable('kb_categories')){
                        $checkKbCategoryExit=KbCategory::count();
                        if($checkKbCategoryExit>0){
                            $getCategoryItmes=KbCategory::with('articles')->where('mailbox_id',$mailbox->id)->paginate($kbCategryLimit);
                            $kbCategry="";
                            if($getCategoryItmes){
                                foreach($getCategoryItmes as $getCategoryItme){
                                    $kbCategry.="<li><h3>".$getCategoryItme->name."</h3><li>";
                                    if(count($getCategoryItme->articles)>0){
                                        $getCategoryItmeArticles=$getCategoryItme->articles()->paginate($kbArticleLimit);
                                        foreach($getCategoryItmeArticles as $article){
                                            if($article->status==2){
                                                $kbCategry.="<div class='kb-articles eupWithKbArticle'><a href='".url('hc/'.$hcId.'/article/'.$article->id.'?category_id='.$getCategoryItme->id)."' target='_blank'><small class='glyphicon glyphicon-list-alt'></small> ".$article->title."</a></div>";
                                            }
                                        }
                                        $kbCategry.="<div style='padding-top:5px;'><a href='".url('hc/'.$hcId.'/category/'.$getCategoryItme->id)."' target='_blank'>View All</a></div>";   
                                    }
                                    
                                }
                            }
                            $removeSpacilacharacter=str_replace('"',"'",$kbCategry);
                            //KB section inside EUP module
                            $kb_section_html="<div class='col-sm-8 col-md-6 col-lg-5' style='margin-left: 5%;margin-right: 5%;'><div class='panel panel-default panel-wizard'><div class='panel-body margin-top-0s'><div class='wizard-header padding-top-0'><h1>Knowledge Base</h1></div><div class='wizard-body'><div class='row'><div class='col-xs-12'><ul style='list-style: none;'>".$removeSpacilacharacter."</ul></div></div></div></div></div></div>";
                        }else{
                            $kb_section_html='';
                        } 
                    }else{
                        $kb_section_html='';
                    }
                    
                   $customFields=json_decode($checkPowerPack->custom_fields);
                    if($customFields){
                        $htmlElement="";

                         foreach($customFields as $customField){
                            if(Schema::hasTable('custom_fields')){
                                $custom_fields=DB::table('custom_fields')->where('id',$customField)->first();
                                if($custom_fields){
                                    if($custom_fields->type==\Modules\CustomFields\Entities\CustomField::TYPE_SINGLE_LINE){
                                    $type='text';
                                    $placeholder=$custom_fields->name;
                                    $custom_field_id=$custom_fields->id;
                                    $htmlElement.="<div class='form-group'><input type='".$type."' name='pp_custom_field_".$custom_field_id."' class='form-control eup-remember input-md' placeholder='".$placeholder."'></div>";
                                    }elseif($custom_fields->type==\Modules\CustomFields\Entities\CustomField::TYPE_DROPDOWN){
                                        $custom_field_id=$custom_fields->id;
                                        $option="<option></option>";
                                        if($custom_fields->options){
                                            foreach(json_decode($custom_fields->options) as $key=>$options){
                                                $option.="<option value=".$key.">".$options."</option>";
                                            }
                                        }
                                        $htmlElement.="<div class='form-group'><select name='pp_custom_field_".$custom_field_id."' class='form-control eup-remember input-md'>".$option."</select></div>";
                                        
                                    }
                                } 

                            }
                        }
                        echo "<script>var html=\"$htmlElement\"</script>";
                    }else{
                        $htmlElement='';
                        echo "<script>var html=\"$htmlElement\"</script>";
                    } 
                    echo "<script>var customHtml=\"$customHtml\"</script>";
                    echo "<script>var logoImageEup=\"$logoImageEup\"</script>";
                    echo "<script>var enable_kb_section=\"$enable_kb_section\"</script>";
                    echo "<script>var kb_section_html=\"$kb_section_html\"</script>";
                        
                }
            }else{
                $link = \Request::url();
                $split = explode("hc/", $link);
                $count = count($split);
                if($count > 1){
                  $mailboxId=explode('/',$split[1]);
                    if($mailboxId[0]){
                        $mailbox = $this->processMailboxId($mailboxId[0]);
                        if($mailbox){
                            $checkPowerPack=PowerPack::where('mailbox_id',$mailbox->id)->first();
                            if($checkPowerPack){
                                if($checkPowerPack->kbLogoImage && $checkPowerPack->kb_enable_text_logo==1 && $checkPowerPack->kbLogoText){
                                    $kbLogoImage=asset('img/'.$checkPowerPack->kbLogoImage);
                                    $kbLogoText=$checkPowerPack->kbLogoText;
                                    $logoImageKb="<img src='".$kbLogoImage."' height='100%'><span style='padding-left:5px;'>".$kbLogoText."</span>";
                                }elseif($checkPowerPack->kbLogoImage){
                                    $kbLogoImage=asset('img/'.$checkPowerPack->kbLogoImage);
                                    $logoImageKb="<img src='".$kbLogoImage."' height='100%'>";
                                }elseif($checkPowerPack->enable_text_logo==1 && $checkPowerPack->kbLogoText){
                                    $kbLogoText=$checkPowerPack->kbLogoText;
                                    $logoImageKb="<span>".$kbLogoText."</span>";
                                }else{
                                     $logoImageKb='';
                                } 
                                echo "<script>var logoImageKb=\"$logoImageKb\"</script>";
                            }
                        }
                    }
                }
            }
           
        });

        // Add module's JS file to the application layout.
        \Eventy::addFilter('eup.javascripts', function($javascripts) {
            $javascripts[] = \Module::getPublicPath(PP_MODULE).'/js/laroute.js';
            $javascripts[] = \Module::getPublicPath(PP_MODULE).'/js/main.js';
            return $javascripts;
        });
        // Add module's JS file to the application layout.
        \Eventy::addFilter('eup.widget_form.javascripts', function($javascripts) {
            $javascripts[] = \Module::getPublicPath(PP_MODULE).'/js/laroute.js';
            $javascripts[] = \Module::getPublicPath(PP_MODULE).'/js/module.js';
            return $javascripts;
        });
        // Add module's JS file to the application layout.
        \Eventy::addFilter('kb.widget_form.javascripts', function($javascripts) {
            $javascripts[] = \Module::getPublicPath(PP_MODULE).'/js/laroute.js';
            $javascripts[] = \Module::getPublicPath(PP_MODULE).'/js/module.js';
            $javascripts[] = \Module::getPublicPath(PP_MODULE).'/js/main.js';
            return $javascripts;
        });
        // Add module's JS file to the application layout.
        \Eventy::addFilter('kb.javascripts', function($javascripts) {
            $javascripts[] = \Module::getPublicPath(PP_MODULE).'/js/laroute.js';
            $javascripts[] = \Module::getPublicPath(PP_MODULE).'/js/module.js';
            $javascripts[] = \Module::getPublicPath(PP_MODULE).'/js/main.js';
            return $javascripts;
        });
    }
     // todo: mailbox should be active.
    public function processMailboxId($mailbox_id, $extra_salt = '')
    {
        try {
            $mailbox_id = $this->decodeMailboxId($mailbox_id, $extra_salt);
            if ($mailbox_id) {
                $mailbox = Mailbox::findOrFail($mailbox_id);
            } 
        } catch (\Exception $e) {
            return null;
        }

        if (empty($mailbox)) {
            return null;
        }

        return $mailbox;
    }
    public  function encodeMailboxId($id, $extra_salt = '')
    {   
        return crc32(config('app.key').'enduserportal'.$extra_salt.$id);
    }
    public  function decodeMailboxId($encoded_id, $extra_salt = '')
    {   
        $result = '';
        $mailboxes = Mailbox::get();
        foreach ($mailboxes as $mailbox) {
            $cur_encoded_id = self::encodeMailboxId($mailbox->id, $extra_salt);
            self::$mailboxes_ids[$cur_encoded_id] = $mailbox->id;
            if ($cur_encoded_id == $encoded_id) {
                $result = $mailbox->id;
            }
        }
        return $result;
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
            __DIR__.'/../Config/config.php' => config_path('powerpack.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'powerpack'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/powerpack');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/powerpack';
        }, \Config::get('view.paths')), [$sourcePath]), 'powerpack');
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
