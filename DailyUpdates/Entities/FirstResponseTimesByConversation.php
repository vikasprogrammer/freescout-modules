<?php
namespace Modules\DailyUpdates\Entities;

use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;

class FirstResponseTimesByConversation extends Model
{  
	protected $table = 'first_response_times_by_conversations';
}
