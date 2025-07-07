<?php

namespace App\Models\WTF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardisedActivityTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_standardised_activity_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'duration',
        'is_divisible',
        'people',
    ];  

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_divisible' => 'boolean',
        ];
    }    

    public function group(): BelongsTo
    {
        return $this->belongsTo(StandardisedActivityGroup::class, "group_id");
    }    
}
