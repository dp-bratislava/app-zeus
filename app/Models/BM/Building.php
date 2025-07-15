<?php

namespace App\Models\BM;

use App\Models\TS\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_bm_buildings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
        'description',
        'compound_id',
    ];

    public function compound(): BelongsTo
    {
        return $this->belongsTo(Compound::class, "compound_id");
    }   

    public function offices(): HasMany
    {
        return $this->hasMany(Office::class, "building_id");
    }     

    public function tickets(): MorphToMany
    {
        return $this->morphToMany(
            Ticket::class, 
            'subject',
            'dpb_ts_ticket_subjects',
            'subject_id',
            'ticket_id',
        );
    }    
}
