<?php

namespace Modules\DailyUpdates\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Mailbox;
use App\Attachment;
use App\Conversation;
use App\Customer;
use App\Email;
use App\Thread;
use App\User;
use Validator;
use Carbon\Carbon;
use Modules\DailyUpdates\Entities\CustomDailyUpdate;
use Modules\DailyUpdates\Entities\UserDailySendReply;
use Modules\DailyUpdates\Entities\FirstResponseTime;
use Modules\DailyUpdates\Entities\FirstResponseTimesByConversation;
use Modules\DailyUpdates\Entities\FirstResponseTimesByMailbox;
use Modules\DailyUpdates\Entities\CustomerWatingTicket;
use Modules\DailyUpdates\Entities\UserFirstResponseTime;
use Modules\DailyUpdates\Entities\BusyTicketNotification;
use Modules\DailyUpdates\Entities\BusyTicket;
class DailyUpdatesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function dailyupdates(Request $request)
    {   
        if($request->isMethod('post')){
            $checkDailyUpdateEdxit=CustomDailyUpdate::count();
            if($checkDailyUpdateEdxit>0){
                $updateDailyUpadte=CustomDailyUpdate::first();
                $updateDailyUpadte->slack_url=$request->slack_url;
                $updateDailyUpadte->enable_slack_notification=$request->enable_slack_notification;
                $updateDailyUpadte->mailboxes=json_encode($request->mailboxes);
                $updateDailyUpadte->users=json_encode($request->users);
                $updateDailyUpadte->save();
            }else{
                $newDailyUpdate=new CustomDailyUpdate();
                $newDailyUpdate->slack_url=$request->slack_url;
                $newDailyUpdate->enable_slack_notification=$request->enable_slack_notification;
                $newDailyUpdate->mailboxes=json_encode($request->mailboxes);
                $newDailyUpdate->users=json_encode($request->users);
                $newDailyUpdate->save();
            }
        }
        $users=User::where(['role'=>1,'status'=>1])->get();
        $dailyUpdate=CustomDailyUpdate::first();
        $allMailboxs = Mailbox::get();
        return view('dailyupdates::settings',compact('dailyUpdate','users','allMailboxs'));
    }
    public function usersMailboxesDailyupdates(Request $request){
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
                                                //$conversationsCount=Conversation::with('threads')->where('state','!=',3)->where('status','!=',3)->where(['mailbox_id'=>$mailbox->id,'user_id'=>$user->id])->where('updated_at', '>=', Carbon::now()->subDay())->count();
                                                $userSendReply->count=$conversationsCount;
                                                $userSendReply->save();

                                                /*First Response Time */
                                                //$conversations=Conversation::with('threads')->where('state','!=',3)->where('status','!=',3)->where(['mailbox_id'=>$mailbox->id,'user_id'=>$user->id])->whereDate('updated_at', '=', Carbon::today()->toDateString())->get();
                                                $conversations=Conversation::with('threads')->where('state','!=',3)->where('status','!=',3)->where('status','!=',4)->where(['mailbox_id'=>$mailbox->id,'user_id'=>$user->id])->whereDate("updated_at",">",Carbon::now()->subDay())->whereDate("updated_at","<",Carbon::now())->get();
                                                    if($conversations){
                                                     foreach($conversations as $conversation){
                                                        /*user first response time start*/
                                                        //$userFirstThread=Thread::where(['conversation_id'=>$conversation->id])->whereNotNull('created_by_user_id')->where('first','!=',1)->whereDate('created_at', '=', Carbon::today()->toDateString())->orderBy('created_at', 'asc')->first();
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
        //dd($userDatas);
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
                    $minutesTicket    = floor(($initTicket / 60) % 60);
                    $finalResponseTimeDataTicket=$hoursTicket.' Hours '.$minutesTicket.' Minutes';
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
                   /*
                    $data = array(
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
                    dd($json_string);
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
    /*customer waiting for ticket start*/
    public function customerTicketWaiting(Request $request){
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
                        dd($json_string);
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
                    dd($json_string);
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
    /*customer waiting for ticket end*/
        /*busy ticket notification start*/
    public function mailboxBusyTicketNotification(Request $request){
        
       if($request->isMethod('post')){
            BusyTicketNotification::truncate();
            $checkDailyUpdateEdxit=BusyTicketNotification::count();
            if($checkDailyUpdateEdxit>0){
                $updateBusyTicketNotification=BusyTicketNotification::first();
                if($updateBusyTicketNotification){
                    $updateBusyTicketNotification->times=$request->times;
                    $updateBusyTicketNotification->save();
                }
            }else{
                $newDailyUpdate=new BusyTicketNotification();
                $newDailyUpdate->times=$request->times;
                $newDailyUpdate->save();
            }
        }
        $users=User::where(['role'=>1,'status'=>1])->get();
        $busyTicketNotification=BusyTicketNotification::first();
        $allMailboxs = Mailbox::get();
        return view('dailyupdates::busyTicketNotification',compact('busyTicketNotification','users','allMailboxs'));
    }
    /*busy ticket notification end*/
    public function busyTicketNotification(Request $request){
        BusyTicket::truncate();
        $conversations=Conversation::with('threads')->where('state','!=',3)->where('status','!=',3)->where('status','!=',4)->whereDate('updated_at', '=', Carbon::today()->toDateString())->get();
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
                "text" => "*Busy Ticket Notification * \n ===============".nl2br($busyTicketString),
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
                dd($json_string);
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
