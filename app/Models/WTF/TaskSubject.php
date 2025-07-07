<?php

namespace App\Models\WTF;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskSubject extends MorphPivot
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_task_subjects';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'subject_id',
        'subject_type',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, "task_id");
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
