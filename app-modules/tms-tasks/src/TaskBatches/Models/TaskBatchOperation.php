<?php

namespace Dpb\Modules\Tasks\TaskBatches\Models;

use Dpb\WorkTimeFund\Models\Operation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskBatchOperation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_batch_id',
        'wtf_operation_id',
    ];

    public function getTable()
    {
        return config('pkg-task-ms.table_prefix') . 'task_batch_operations';
    }

    public function taskBatch(): BelongsTo
    {
        return $this->belongsTo(TaskBatch::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(
            Operation::class,
            'wtf_operation_id'
        );
    }
}
