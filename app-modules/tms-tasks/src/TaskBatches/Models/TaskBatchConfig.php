<?php

namespace Dpb\Modules\Tasks\TaskBatches\Models;

use Dpb\Package\Tasks\Models\TaskItemGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TaskBatchConfig extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_batch_id',
        'subject_id',
        'subject_type',
        'task_item_group_id',
        'quantity',
    ];

    public function getTable()
    {
        return config('pkg-task-ms.table_prefix') . 'task_batch_config';
    }

    public function taskBatch(): BelongsTo
    {
        return $this->belongsTo(TaskBatch::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function taskItemGroups(): BelongsTo
    {
        return $this->belongsTo(
            TaskItemGroup::class,
            'task-item_group_id'
        );
    }
}
