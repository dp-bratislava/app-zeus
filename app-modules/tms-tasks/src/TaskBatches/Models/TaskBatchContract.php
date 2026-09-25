<?php

namespace Dpb\Modules\Tasks\TaskBatches\Models;

use App\Models\Datahub\EmployeeContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskBatchContract extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_batch_id',
        'employee_contract_id',
    ];

    public function getTable()
    {
        return config('pkg-task-ms.table_prefix') . 'task_batch_contracts';
    }

    public function taskBatch(): BelongsTo
    {
        return $this->belongsTo(TaskBatch::class);
    }

    public function contracts(): BelongsTo
    {
        return $this->belongsTo(
            EmployeeContract::class,
            'employee_contract_id'
        );
    }
}
