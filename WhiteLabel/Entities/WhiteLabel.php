<?php
/**
 * Outgoing emails.
 */

namespace Modules\WhiteLabel\Entities;

use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;

class WhiteLabel extends Model
{   
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $table = 'white_labels';

    
}
