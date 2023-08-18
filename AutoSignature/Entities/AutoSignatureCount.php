<?php
/**
 * Outgoing emails.
 */

namespace Modules\AutoSignature\Entities;

use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\AutoSignature\Entities\AutoSignature;
class AutoSignatureCount extends Model
{
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */

    protected $table = 'auto_signature_counts';

}
