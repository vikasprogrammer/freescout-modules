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
use Modules\DailyUpdates\Entities\UserFirstResponseTime;
class DailyUpdate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'freescout:daily-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch all users mailboxs conversation conut daily ';

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
        UserDailySendReply::truncate();
        UserFirstResponseTime::truncate();
        $getAssignUsers=CustomDailyUpdate::first();
        if($getAssignUsers){
            if($getAssignUsers->users){
                if($getAssignUsers->users !='null'){
                    foreach(json_decode($getAssignUsers->users) as $user){
                        $users=User::with('conversations','mailboxes')->where(['id'=>$user,'role'=>1,'status'=>1])->get();
                        if($users){
                            foreach ($users as $user) {
                                if($user->mailboxes){
                                    $data="";
                                    foreach($user->mailboxes as $mailbox){
                                        if($getAssignUsers->mailboxes !='null'){
                                            if(in_array($mailbox->id,json_decode($getAssignUsers->mailboxes))){
                                                /*Reply Count*/
                                                $userSendReply= new UserDailySendReply();
                                                $userName=$user->first_name.' '.$user->last_name;
                                                $userSendReply->user_name=$userName;
                                                $userSendReply->mailbox_name=$mailbox->name;
                                                $conversations=Conversation::with('threads')->where('state','!=',3)->where('status','!=',3)->where('status','!=',4)->where(['mailbox_id'=>$mailbox->id,'user_id'=>$user->id])->whereDate("updated_at",">",Carbon::now()->subDay())->whereDate("updated_at","<",Carbon::now())->get();
                                                
                                                if($conversations){
                                                    foreach($conversations as $conversation){
                                                        $conversationThreadCount[]=Thread::where(['conversation_id'=>$conversation->id])->where('status',2)->where(['created_by_user_id'=>$user->id])->whereDate("updated_at",">",Carbon::now()->subDay())->whereDate("updated_at","<",Carbon::now())->count();
                                                    } 
                                                    if(isset($conversationThreadCount)){
                                                        $userSendReply->threadCount=array_sum($conversationThreadCount);
                                                    }
                                                    unset($conversationThreadCount);
                                                }
                                                $conversationsCount=Conversation::with('threads')->where('state','!=',3)->where('status','!=',3)->where('status','!=',4)->where(['mailbox_id'=>$mailbox->id,'user_id'=>$user->id])->whereDate("updated_at",">",Carbon::now()->subDay())->whereDate("updated_at","<",Carbon::now())->count();
                                                //$conversationsCount=Conversation::with('threads')->where('state','!=',3)->where('status','!=',3)->where('status','!=',4)->where(['mailbox_id'=>$mailbox->id,'user_id'=>$user->id])->where("updated_at",">",Carbon::now()->subDay())->where("updated_at","<",Carbon::now())->count();
                                                $userSendReply->count=$conversationsCount;
                                                $userSendReply->save();

                                                /*First Response Time */
                                                $conversations=Conversation::with('threads')->where('state','!=',3)->where('status','!=',3)->where('status','!=',4)->where(['mailbox_id'=>$mailbox->id,'user_id'=>$user->id])->where("updated_at",">",Carbon::now()->subDay())->where("updated_at","<",Carbon::now())->get();
                                                    if($conversations){
                                                     foreach($conversations as $conversation){
                                                        /*user first response time start*/
                                                        $userFirstThread=Thread::where(['conversation_id'=>$conversation->id])->whereNotNull('created_by_user_id')->where('first','!=',1)->orderBy('created_at', 'asc')->first();
                                                        if($userFirstThread){
                                                            $userFirstResponseTimeNew=new UserFirstResponseTime();
                                                            $userFirstResponseTimeNew->mailbox_id=$mailbox->id;
                                                            $userFirstResponseTimeNew->conversation_id=$conversation->id;
                                                          
                                                            $timeArraySeconds=strtotime(date('Y-m-d H:i:s'))-strtotime($userFirstThread->created_at->format('Y-m-d H:i:s'));
                                                            $userFirstResponseTimeNew->times=$timeArraySeconds;
                                                            $userFirstResponseTimeNew->save();
                                                        }
                                                        /*user first response time end*/
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
            }
        }

        //send notification
        $userDatas=UserDailySendReply::get();
        if(count($userDatas)>0){
            foreach ($userDatas as $data) {
                if($data->count !=0){
                     if($data->threadCount>=$data->count){
                        $threadCount=$data->threadCount?($data->threadCount>3)?$data->threadCount .' Replies':$data->threadCount.' Reply':'1 Reply';
                    }else{
                        $threadCount=$data->count?($data->count>3)?$data->count .' Replies':$data->count.' Reply':'1 Reply';
                    }
                    $count=$data->count?($data->count>3)?$data->count .' Conversations':$data->count.' Conversation':'No Conversation';
                    //$threadCount=$data->threadCount?($data->threadCount>3)?$data->threadCount .' Replies':$data->threadCount.' Reply':'No Reply';
                    $neweArray[]='@'.$data->user_name.', '.$data->mailbox_name.' - '.$count.' ('.$threadCount.')';
                    //$neweArray[]='@'.$data->user_name.', '.$data->mailbox_name.' - '.$count;
                }
            }
        }else{
            $neweArray=[];
        }
        $responseTimes=UserFirstResponseTime::groupBy('mailbox_id')->whereDate('created_at', '=', Carbon::today()->toDateString())->get();
        if(count($responseTimes)>0){
            foreach ($responseTimes as $responseTime) {
                $increseResponseTimes=UserFirstResponseTime::where('mailbox_id',$responseTime->mailbox_id)->whereDate('created_at', '=', Carbon::today()->toDateString())->where('times','!=','0')->get();
                foreach($increseResponseTimes as $increseResponseTime) {
                    $getConversation=Conversation::find($increseResponseTime->conversation_id);
                    $conversationUrl= "<".$getConversation->url()."| # ".$increseResponseTime->conversation_id.">";
                    $initTicket       = $increseResponseTime->times?$increseResponseTime->times:0;
                    $hoursTicket      = floor($initTicket / 3600);
                    $minutesTicket     = floor(($initTicket / 60) % 60);
                    $finalResponseTimeDataTicket=$hoursTicket.' Hours ';
                    //$finalResponseTimeDataTicket=$hoursTicket.' Hours '.$minutesTicket.' Minutes';
                    $finalResponseTimeTicket=$conversationUrl;
                    $ticketWating[]=$finalResponseTimeTicket.' - '.$finalResponseTimeDataTicket;
                }
                /*mailbox */
                $mailboxName =Mailbox::find($responseTime->mailbox_id);
                $nameMailbox =$mailboxName?$mailboxName->name:'';
                $timeArray = UserFirstResponseTime::where('mailbox_id',$responseTime->mailbox_id)->pluck('times')->toArray();
                $average = array_sum($timeArray)/count($timeArray);
                $init        = $average;
                $hours       = floor($init / 3600);
                $minutes     = floor(($init / 60) % 60);
                $seconds     = $init % 60;
                $finalResponseTime=$hours.' Hours '.$minutes.' Minutes '.$seconds.' Seconds ';
                $ticketWatingData=str_replace(array('[',']','"'), '',json_encode($ticketWating));
                $neweArrayresponseTimes[]='@'.$nameMailbox.' - '.$finalResponseTime.'@ ('.$ticketWatingData.')';
                unset($ticketWating);
            }
        }else{
            $neweArrayresponseTimes=[];
        }
        if($neweArray && $neweArrayresponseTimes){
            $settingDailyUpdate=CustomDailyUpdate::first();
            if($settingDailyUpdate){
                if($settingDailyUpdate->enable_slack_notification=='1' && $settingDailyUpdate->mailboxes !='null' && $settingDailyUpdate->users !='null' ){
                    $userRecords=str_replace(array('[',']','"'), ' ', json_encode($neweArray));
                    $arrayresponseTimesData=str_replace(array('[',']','"'), ' ', json_encode($neweArrayresponseTimes));
                   
                    /*$data = array(
                    "attachments" => array(
                        array(
                            "color" => "#b0c4de",
                            "fallback" => 'Daily Updates',
                            "text" => "Daily Updates \n ===============\n\n *Reply Count * ".nl2br($userRecords)."\n\n *First Response Time *".nl2br($arrayresponseTimesData),
                            )
                        )
                    );*/

                    $data = array(
                    "attachments" => array(
                        array(
                            "color" => "#b0c4de",
                            "fallback" => 'Daily Updates',
                            "text" => "Daily Updates \n ===============\n\n *Reply Count * ".nl2br($userRecords),
                            )
                        )
                    );
                    $json_string_data = json_encode($data);
                    $removeLastComma=str_replace(' ,', ' ', $json_string_data);
                    $json_string_with=str_replace('@', '\n ', $removeLastComma);
                    $json_string1=str_replace('###', ' ', $json_string_with);
                    $json_string2 = str_ireplace('\\\/', '##1', $json_string1);
                    $json_string2 = str_ireplace('\\\/', '##1', $json_string1);
                    $json_string3 = str_ireplace('##1', '/', $json_string2);
                    $json_string = str_ireplace('\\\\', '', $json_string3);
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
