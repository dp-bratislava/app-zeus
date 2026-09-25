<?php

namespace Dpb\Modules\Tasks\TaskBatches\Models;

use App\Models\User;
use Dpb\Package\Batchable\Models\Batch;
use Dpb\Package\Tasks\Models\TaskGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskBatch extends Model
{
    // use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'batch_id',
        'task_group_id',
        'author_id',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function getTable()
    {
        return config('pkg-task-ms.table_prefix') . 'task_batches';
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(
            Batch::class,
            'batch_id'
        );
    }

    public function taskGroup(): BelongsTo
    {
        return $this->belongsTo(
            TaskGroup::class,
            'task_group_id'
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'author_id'
        );
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(TaskBatchTaskSubject::class);
    }

    public function itemGroups(): HasMany
    {
        return $this->hasMany(TaskBatchTaskItemGroup::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(TaskBatchContract::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(TaskBatchOperation::class);
    }


    public function hasTaskWithNoWork(): bool
    {
        return false;
    }
}
