<?php

namespace Modules\PowerPack\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\PowerPack\Entities\PowerPack;
use App\Mailbox;
use App\Attachment;
use App\Conversation;
use App\Customer;
use App\Thread;
class ChatAutoReply extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'freescout:powerpack-process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Powerpack Command description.';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $latestThread=Thread::orderBy('id', 'DESC')->first();
        $checkChatConversationOrNot=Conversation::where(['id'=>$latestThread->conversation_id,'channel'=>1])->first();
        if($checkChatConversationOrNot){
            $checkPowerPack=PowerPack::where('mailbox_id',$checkChatConversationOrNot->mailbox_id)->first();
            if($checkPowerPack){
                if($conversation->last_reply_at < Carbon::now()->subMinutes($checkPowerPack->minutes)){
                   $createThread= new Thread();
                   $createThread->conversation_id=$latestThread->conversation_id;
                   $createThread->type='1';
                   $createThread->body=$checkPowerPack->chat_message;
                   $createThread->has_attachments=$checkChatConversationOrNot->has_attachments;
                   $createThread->source_via=$latestThread->source_via;
                   $createThread->source_type=$latestThread->source_type;
                   $createThread->customer_id=$latestThread->customer_id;
                   $createThread->created_by_customer_id=$latestThread->created_by_customer_id;
                   $createThread->save();
                }     
            }
        }
    }

   
}
