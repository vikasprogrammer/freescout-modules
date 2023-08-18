<?php
/**
 * Outgoing emails.
 */

namespace Modules\AutoSignature\Entities;

use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;

class AutoSignature extends Model
{   
    // User permission.
    const PERM_EDIT_AUTOSIGNATURE = 12;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $table = 'auto_signatures';

    protected static function boot()
    {
        parent::boot();

        self::creating(function (AutoSignature $model) {
            $model->sort_order = AutoSignature::where('mailbox_id', $model->mailbox_id)->max('sort_order')+1;
        });
    }

    /**
     * Get mailbox.
     */
    public function mailbox()
    {
        return $this->belongsTo('App\Mailbox');
    }

    /**
     * Threads created from saved reply.
     */
    public function threads()
    {
        return $this->hasMany('App\Thread');
    }

    public static function userCanUpdateMailboxSavedReplies(User $user, Mailbox $mailbox)
    {
        if ($user->isAdmin() || $mailbox->userHasAccess($user->id)) {
            return true;
        } else {
            return false;
        }
    }
    public function countAutoSignature(){
        return $this->hasOne('Modules\AutoSignature\Entities\AutoSignatureCount','auto_signature_id','id');
    }
}
