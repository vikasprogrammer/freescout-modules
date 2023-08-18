<?php

namespace Modules\PowerPack\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mailbox;
use App\MailboxUser;
use App\Attachment;
use App\Conversation;
use App\Customer;
use App\Thread;
use App\Events\UserReplied;
use Modules\PowerPack\Entities\PowerPack;
use Carbon\Carbon;
class ChatAutoReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $conversation = null,$thread;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($conversation,$thread)
    {
        $this->conversation=$conversation;
        $this->thread=$thread;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $latestThread=Thread::orderBy('id','DESC')->first();
        if($latestThread){
            $checkChatConversationOrNot=Conversation::where(['id'=>$latestThread->conversation_id,'channel'=>1])->first();
            if($checkChatConversationOrNot){
                $checkPowerPack=PowerPack::where('mailbox_id',$checkChatConversationOrNot->mailbox_id)->first();
                if($checkPowerPack){
                    if($latestThread->body != $checkPowerPack->chat_message){
                        if(is_null($latestThread->created_by_user_id)){
                            $getUserId=MailboxUser::where('mailbox_id',$checkChatConversationOrNot->mailbox_id)->first();
                           // if($this->conversation->last_reply_at < Carbon::now()->addMinutes($checkPowerPack->minutes)){
                                $createThread= new Thread();
                                $createThread->conversation_id=$latestThread->conversation_id;
                                $createThread->type = '2';
                                $createThread->status = '2';
                                $createThread->state = '2';
                                if($checkChatConversationOrNot->user_id){
                                    $createThread->user_id=$checkChatConversationOrNot->user_id;
                                    $createThread->created_by_user_id=$checkChatConversationOrNot->user_id;
                                }else{
                                    $createThread->user_id=$getUserId->user_id;
                                    $createThread->created_by_user_id=$getUserId->user_id;
                                }
                                
                                $createThread->body=$checkPowerPack->chat_message;
                                $createThread->has_attachments=$checkChatConversationOrNot->has_attachments;
                                $createThread->source_via='2';
                                $createThread->source_type=$checkChatConversationOrNot->source_type;
                                $createThread->customer_id=$checkChatConversationOrNot->customer_id;
                                $createThread->save();
                                $lastThreadId=$createThread->id;
                                event(new UserReplied($this->conversation, $this->thread));
                                \Eventy::action('conversation.user_replied_can_undo', $this->conversation, $this->thread);
                                // After Conversation::UNDO_TIMOUT period trigger final event.
                                \Helper::backgroundAction('conversation.user_replied', [$this->conversation, $this->thread], now()->addSeconds(Conversation::UNDO_TIMOUT));
                                 // Clear cache.
                                \Cache::forget('chat.threads_'.encrypt($lastThreadId));
                           // }
                        }
                    } 
                }     
            }  
        }
        //self::dispatch($this->conversation,$this->thread)->delay(Carbon::now()->addMinutes(5));
    }
}
