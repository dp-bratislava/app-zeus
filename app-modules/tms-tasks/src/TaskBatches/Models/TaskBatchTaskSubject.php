<?php

namespace Dpb\Modules\Tasks\TaskBatches\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskBatchTaskSubject extends Model
{
    // use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_batch_id',
        'subject_id',
        'subject_type',
    ];

    public function getTable()
    {
        return config('pkg-task-ms.table_prefix') . 'task_batch_task_subjects';
    }

    public function taskBatch(): BelongsTo
    {
        return $this->belongsTo(TaskBatch::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
