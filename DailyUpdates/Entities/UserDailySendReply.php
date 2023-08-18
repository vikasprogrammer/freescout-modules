<?php
namespace Modules\DailyUpdates\Entities;

use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;

class UserDailySendReply extends Model
{  
	protected $table = 'user_daily_send_replies';
}
