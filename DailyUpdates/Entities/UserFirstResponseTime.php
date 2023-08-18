<?php
namespace Modules\DailyUpdates\Entities;

use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;

class UserFirstResponseTime extends Model
{  
	protected $table = 'user_first_response_times';
}
