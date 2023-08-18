<?php
namespace Modules\DailyUpdates\Entities;

use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;

class CustomDailyUpdate extends Model
{  
	protected $table = 'daily_updates';
}
