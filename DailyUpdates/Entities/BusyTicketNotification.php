<?php
namespace Modules\DailyUpdates\Entities;

use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;

class BusyTicketNotification extends Model
{  
	protected $table = 'busy_ticket_notifications';
}
