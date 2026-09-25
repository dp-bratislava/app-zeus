<?php

namespace Dpb\Modules\Tasks\TaskBatches\Models;

use Dpb\Package\Tasks\Models\TaskItemGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskBatchTaskItemGroup extends Model
{
    // use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_batch_id',
        'task_item_group_id',
    ];

    public function getTable()
    {
        return config('pkg-task-ms.table_prefix') . 'task_batch_task_item_groups';
    }

    public function taskBatch(): BelongsTo
    {
        return $this->belongsTo(TaskBatch::class);
    }

    public function taskItemGroup(): BelongsTo
    {
        return $this->belongsTo(
            TaskItemGroup::class,
            'task_item_group_id'
        );
    }
}
