<?php

namespace App\Models\WTF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_activity_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'is_locked',
        'activity_id',
        'group_id',
        'type_id',
        'created_at',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ActivityGroup::class, "group_id");
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class, "type_id");
    }

    // public function durations(): HasMany
    // {
    //     return $this->hasMany(ActivityDuration::class, "activity_id");
    // }    
}
