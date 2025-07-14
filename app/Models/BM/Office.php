<?php

namespace App\Models\BM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_bm_offices';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'description',
        'floor',
        'building_id',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, "building_id");
    }   
}
