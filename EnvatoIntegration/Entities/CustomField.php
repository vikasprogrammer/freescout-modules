<?php

namespace Modules\EnvatoIntegration\Entities;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use Session;
class CustomField extends Model
{
    protected $table = 'custom_fields';
    
    public $timestamps = false; 
    
    public static function getMailboxCustomFields($mailbox_id, $cache = false)
    {
        $query = CustomField::where('mailbox_id', $mailbox_id)
            ->orderby('sort_order');
        if ($cache) {
            $query->rememberForever();
        }
        return $query->get();
    }


}