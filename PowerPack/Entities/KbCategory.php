<?php
/**
 * Outgoing emails.
 */
namespace Modules\PowerPack\Entities;
use App\Mailbox;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\PowerPack\Entities\PowerPack;
class KbCategory extends Model
{
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $table = 'kb_categories';
    /**
     * Returns a shorter value than encrypt().
     */
    
    public function articles()
    {
        return $this->belongsToMany('Modules\PowerPack\Entities\KbArticle')
            // pivot.sort_order.
            ->withPivot('sort_order');
    }
   
   
}