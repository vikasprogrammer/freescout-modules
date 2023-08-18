<?php

namespace Modules\DailyUpdates\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Mailbox;
use App\Conversation;
use App\Customer;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Modules\DailyUpdates\Entities\CustomDailyUpdate;
use Modules\DailyUpdates\Entities\UserDailySendReply;
use Modules\DailyUpdates\Entities\FirstResponseTime;
use Modules\DailyUpdates\Entities\FirstResponseTimesByConversation;
use Modules\DailyUpdates\Entities\FirstResponseTimesByMailbox;
use Modules\DailyUpdates\Entities\CustomerWatingTicket;
class CustomerWaitingTickets extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'freescout:customer-waiting-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Customer waiting for tickets since 4 hours';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       CustomerWatingTicket::truncate();
        $getAssignUsers=CustomDailyUpdate::first();
        if($getAssignUsers){
            if($getAssignUsers->users){
                if($getAssignUsers->users !='null'){
                    foreach(json_decode($getAssignUsers->users) as $user){
                        $users=User::with('conversations','mailboxes')->where(['id'=>$user,'role'=>1,'status'=>1])->get();
                        if($users){
                            foreach ($users as $user) {
                                if($user->mailboxes){
                                    foreach($user->mailboxes as $mailbox){
                                        if($getAssignUsers->mailboxes !='null'){
                                            if(in_array($mailbox->id,json_decode($getAssignUsers->mailboxes))){
                                                /*First Response Time */
                                               
                                                $conversations=Conversation::with('threads')->where('last_reply_from','!=',$user->id)->where('state','!=',3)->where('status','!=',3)->where('status','!=',4)->where(['mailbox_id'=>$mailbox->id])->whereDate('updated_at', '=', Carbon::today()->toDateString())->get();
                                                    //dd($conversations);
                                                    if($conversations){
                                                     foreach($conversations as $conversation){
                                                        $thread=Thread::orderBy('id', 'DESC')->where(['conversation_id'=>$conversation->id])->whereDate('created_at', '=', Carbon::today()->toDateString())->first();
                                                        if($thread){
                                                            $username='';
                                                            $threadAssign=Thread::orderBy('id', 'DESC')->whereNotNull('body')->where(['conversation_id'=>$conversation->id])->whereDate('created_at', '=', Carbon::today()->toDateString())->first();
                                                                if($threadAssign){
                                                                    if(is_null($threadAssign->created_by_user_id)){
                                                                    $checkExitCustomerWatingTicket1=CustomerWatingTicket::where(['conversation_id'=>$conversation->id,'mailbox_id'=>$mailbox->id])->count();
                                                                    if($checkExitCustomerWatingTicket1>0){
                                                                        
                                                                        $updateFirstResponseTime1=CustomerWatingTicket::where(['conversation_id'=>$conversation->id,'mailbox_id'=>$mailbox->id])->first();
                                                                        $updateFirstResponseTime1->mailbox_id=$mailbox->id;
                                                                        $updateFirstResponseTime1->users_name=$username;
                                                                        $updateFirstResponseTime1->conversation_id=$conversation->id;
                                                                        $timeArraySeconds=strtotime(date('Y-m-d H:i:s'))-strtotime($thread->created_at->format('Y-m-d H:i:s'));
                                                                        $updateFirstResponseTime1->times=$timeArraySeconds;
                                                                        $updateFirstResponseTime1->save(); 
                                                                    }else{
                                                                        $newFirstResponseTime1= new CustomerWatingTicket();
                                                                        $newFirstResponseTime1->mailbox_id=$mailbox->id;
                                                                        $newFirstResponseTime1->users_name=$username;
                                                                        $newFirstResponseTime1->conversation_id=$conversation->id;
                                                                        $timeArraySeconds=strtotime(date('Y-m-d H:i:s'))-strtotime($thread->created_at->format('Y-m-d H:i:s'));
                                                                        $newFirstResponseTime1->times=$timeArraySeconds;
                                                                        $newFirstResponseTime1->save(); 
                                                                    } 
                                                                }
                                                           }
                                                            
                                                        }
                                                        
                                                    }
                                                }
                                                /*First Response Time end*/
                                            } 
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //send notification
        $increseResponseTimesUniqueMailboxs=CustomerWatingTicket::groupBy('mailbox_id')->where('times','!=','0')->get();
       
        if(count($increseResponseTimesUniqueMailboxs)>0){
            foreach($increseResponseTimesUniqueMailboxs as $increseResponseTimesUniqueMailbox){
                $mailboxName=Mailbox::find($increseResponseTimesUniqueMailbox->mailbox_id);
                $nameMailbox=$mailboxName?$mailboxName->name:'';
                $increseResponseTimes=CustomerWatingTicket::where('mailbox_id',$increseResponseTimesUniqueMailbox->mailbox_id)->whereDate('created_at', '=', Carbon::today()->toDateString())->where('times','!=','0')->get();
                
                foreach($increseResponseTimes as $increseResponseTime) {
                    $getConversation=Conversation::find($increseResponseTime->conversation_id);
                    $conversationUrl= "<".$getConversation->url()."| # ".$increseResponseTime->conversation_id.">";
                    $init       = $increseResponseTime->times?$increseResponseTime->times:0;
                    $hours      = floor($init / 3600);
                    $minutes    = floor(($init / 60) % 60);
                    $seconds    = $init % 60;
                    $userName   = $increseResponseTime->users_name;
                    $finalHours = str_replace('-', '',$hours);
                    //dd($hours);
                    if($finalHours>4 && $finalHours<24){
                        if($userName){
                            $finalResponseTime='Conversation '.$conversationUrl.' ('.$finalHours.' Hours $$# '.$userName.')';
                        }else{
                            $finalResponseTime='Conversation '.$conversationUrl.' ('.$finalHours.' Hours '.')';
                        }
                        
                        $increseArrayresponseTimes[]='@'.$nameMailbox.' - '.$finalResponseTime;
                    }
                }
            }
            
        }else{
            $increseArrayresponseTimes=[];
        }
        if(isset($increseArrayresponseTimes) && count($increseArrayresponseTimes)>0){
            
            $settingDailyUpdate=CustomDailyUpdate::first();
            if($settingDailyUpdate){
                if($settingDailyUpdate->enable_slack_notification=='1' && $settingDailyUpdate->mailboxes !='null' && $settingDailyUpdate->users !='null' ){
                    $increseArrayresponseTimesData=trim(str_replace(array('[',']','"'), ' ', json_encode($increseArrayresponseTimes)));
                    $data = array(
                    "attachments" => array(
                        array(
                            "color" => "#b0c4de",
                            "fallback" => 'Per Hour , Customers Waiting Tickets',
                            "text" => "*Customers Waiting Tickets * \n ===============\n".nl2br($increseArrayresponseTimesData),
                            )
                        )
                    );
                    if($increseArrayresponseTimes){
                        $json_string_data = json_encode($data);
                        $removeLastComma=str_replace(' ,', ' ', $json_string_data);
                        $json_string_with=str_replace('@', '\n ', $removeLastComma);
                        $json_string1=str_replace('###', ' ', $json_string_with);
                        $json_string2 = str_ireplace('\\\/', '##1', $json_string1);
                        $json_string3 = str_ireplace('##1', '/', $json_string2);
                        $json_string4 = str_ireplace('\\\\', '', $json_string3);
                        $json_string = str_ireplace('$$#', ',', $json_string4);

                        if($settingDailyUpdate->slack_url){
                            $slack_webhook_url = $settingDailyUpdate->slack_url;
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
        }else{
            /*less than 4 hours */
            $increseResponseTimesUniqueMailboxs=CustomerWatingTicket::groupBy('mailbox_id')->where('times','!=','0')->get();
            if(count($increseResponseTimesUniqueMailboxs)>0){
                foreach($increseResponseTimesUniqueMailboxs as $increseResponseTimesUniqueMailbox){
                    $mailboxName=Mailbox::find($increseResponseTimesUniqueMailbox->mailbox_id);
                    $nameMailbox=$mailboxName?$mailboxName->name:'';
                    $increseResponseTimes=CustomerWatingTicket::where('mailbox_id',$increseResponseTimesUniqueMailbox->mailbox_id)->whereDate('created_at', '=', Carbon::today()->toDateString())->where('times','!=','0')->get();
                    foreach($increseResponseTimes as $increseResponseTime) {
                        $getConversation=Conversation::find($increseResponseTime->conversation_id);
                        $conversationUrl= "<".$getConversation->url()."| # ".$increseResponseTime->conversation_id.">";
                        $init       = $increseResponseTime->times?$increseResponseTime->times:0;
                        $hours      = floor($init / 3600);
                        $minutes    = floor(($init / 60) % 60);
                        $seconds    = $init % 60;
                        $userName   = $increseResponseTime->users_name;
                        $finalHours = str_replace('-', '',$hours);
                        if($finalHours !=0){
                            if($userName){
                                $finalResponseTime='Conversation '.$conversationUrl.' ('.$finalHours.' Hours $$# '.$userName.')';
                            }else{
                                $finalResponseTime='Conversation '.$conversationUrl.' ('.$finalHours.' Hours '.')';
                            }
                            
                            $increseArrayresponseTimes[]='@'.$nameMailbox.' - '.$finalResponseTime;
                        }
                    }
                }
                /*notification setting*/
                if(isset($increseArrayresponseTimes)){
                    $increseArrayresponseTimesData=trim(str_replace(array('[',']','"'), ' ', json_encode($increseArrayresponseTimes)));
                    $data = array(
                    "attachments" => array(
                        array(
                            "color" => "#b0c4de",
                            "fallback" => 'Per Hour , Customers Waiting Tickets',
                            "text" => "*Customers Waiting Tickets * \n ===============\n".nl2br($increseArrayresponseTimesData),
                            )
                        )
                    );
                    $json_string_data = json_encode($data);
                    $removeLastComma=str_replace(' ,', ' ', $json_string_data);
                    $json_string_with=str_replace('@', '\n ', $removeLastComma);
                    $json_string1=str_replace('###', ' ', $json_string_with);
                    $json_string2 = str_ireplace('\\\/', '##1', $json_string1);
                    $json_string3 = str_ireplace('##1', '/', $json_string2);
                    $json_string4 = str_ireplace('\\\\', '', $json_string3);
                    $json_string = str_ireplace('$$#', ',', $json_string4);
                }
            }else{
                $data = array(
                    "attachments" => array(
                        array(
                            "color" => "#b0c4de",
                            "fallback" => 'Per Hour , Customers Waiting Tickets',
                            "text" => "*Customers Waiting Tickets * \n ===============\n :white_check_mark: No pending tickets! Yay, team!",
                            )
                        )
                    );
                $json_string = json_encode($data);
            }
            $settingDailyUpdate=CustomDailyUpdate::first();
            if($settingDailyUpdate){
                if($settingDailyUpdate->enable_slack_notification=='1' && $settingDailyUpdate->mailboxes !='null' && $settingDailyUpdate->users !='null' ){
                    
                    if($settingDailyUpdate->slack_url){
                        $slack_webhook_url = $settingDailyUpdate->slack_url;
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
