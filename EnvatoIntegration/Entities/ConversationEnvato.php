<?php
/**
 * Outgoing emails.
 */

namespace Modules\EnvatoIntegration\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\EnvatoIntegration\Entities\EnvatoCustomField;

class ConversationEnvato extends Model
{
    protected $table = 'conversation_envato';
    
    public $timestamps = false;

    protected $fillable = [
    	'conversation_id', 'custom_field_id', 'value'
    ];

    /**
     * Get conversation.
     */
    public function conversation()
    {
        return $this->belongsTo('App\Conversation');
    }
}
