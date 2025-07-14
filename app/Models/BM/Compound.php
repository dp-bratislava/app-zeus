<?php

namespace App\Models\BM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compound extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_bm_compounds';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
        'description',
    ];

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, "compound_id");
    }   
}
