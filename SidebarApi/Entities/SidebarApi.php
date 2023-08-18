<?php
/**
 * Outgoing emails.
 */

namespace Modules\SidebarApi\Entities;

use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;

class SidebarApi extends Model
{   
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $table = 'sidebar_apis';

    
}
