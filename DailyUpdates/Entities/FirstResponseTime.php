<?php
namespace Modules\DailyUpdates\Entities;

use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;

class FirstResponseTime extends Model
{  
	protected $table = 'first_response_times';
}
