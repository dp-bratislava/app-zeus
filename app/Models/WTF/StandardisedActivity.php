<?php

namespace App\Models\WTF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardisedActivity extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_standardised_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'template_id',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, "task_id");
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(StandardisedActivityTemplate::class, "template_id");
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, "standardised_activity_id");
    }    
}
