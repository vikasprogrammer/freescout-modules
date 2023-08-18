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
use Modules\DailyUpdates\Entities\BusyTicket;
class BusyTicketNotification extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'freescout:busy-ticket-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busy ticket notification description';

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
        BusyTicket::truncate();
        $conversations=Conversation::with('threads')->where('state','!=',3)->where('status','!=',3)->where('status','!=',4)->whereDate('updated_at', '=', Carbon::today()->toDateString())->get();
       //dd($conversations);
        if($conversations){
            foreach($conversations as $conversation){
                $threadCount=Thread::where(['conversation_id'=>$conversation->id])->whereDate('created_at', '=', Carbon::today()->toDateString())->count();
                $newBusyTicket = new BusyTicket();
                $newBusyTicket->subject=$conversation->subject;
                $newBusyTicket->conversation_id=$conversation->id;
                $newBusyTicket->mailbox=$conversation->mailbox_id;
                $newBusyTicket->users=$conversation->user_id;
                $newBusyTicket->counts=$threadCount;
                $newBusyTicket->save();
            }  
        }
        $busyTickets=BusyTicket::get();
        if($busyTickets){
            foreach($busyTickets as $busyTicket){
                $getTimes=BusyTicketNotification::first();
                if($getTimes){
                    if($busyTicket->counts>=$getTimes->times){
                        $mailboxName=Mailbox::find($busyTicket->mailbox);
                        $nameMailbox=$mailboxName?$mailboxName->name:'';
                        $getConversation=Conversation::find($busyTicket->conversation_id);
                        $conversationUrl= "<".$getConversation->url()."| # ".$busyTicket->conversation_id.">";
                        $userdata=User::find($busyTicket->users);
                        if($userdata){
                            $userName   = $userdata->first_name.' '.$userdata->last_name;
                        }else{
                            $userName   = '';   
                        }
                        $subject   = $busyTicket->subject;
                        $conversationId=$conversationUrl;
                        $busyTicketArray[]='@'.$subject .' '.$conversationId.' (By '.$userName.', '.$nameMailbox.')';   
                    }
                }
            }
        }else{
            $busyTicketArray=[];
        }
        $busyTicketString=trim(str_replace(array('[',']','"'), ' ', json_encode($busyTicketArray)));
        $data = array(
        "attachments" => array(
            array(
                "color" => "#b0c4de",
                "fallback" => 'Busy Ticket Notification',
                "text" => "*Busy Ticket Notification * \n ===============\n ".nl2br($busyTicketString),
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
