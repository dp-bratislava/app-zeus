<?php

namespace App\Models\BM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
}
