<?php

namespace Dpb\Modules\Tasks\Eligibility\Models;

use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Dpb\WorkTimeFund\Models\Operation as WtfOperation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * Read only model for db view
 */
class DepartmentOperationRuleView extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [];

    public $timestamps = false;

    protected $primaryKey = null;

    public $incrementing = false;

    protected $casts = [
        'conditions' => 'array',
    ];

    public function getTable()
    {
        return 'vw_' . config('pkg-task-ms.table_prefix') . 'task_batch_department_operation_rules';
    }

    public function save(array $options = [])
    {
        throw new RuntimeException('This model is read-only.');
    }

    public function delete()
    {
        throw new RuntimeException('This model is read-only.');
    }

    public function create()
    {
        throw new RuntimeException('This model is read-only.');
    }

    public function update(array $attributes = [], array $options = []): bool
    {
        throw new RuntimeException('This model is read-only.');
    }        

    public function operation(): BelongsTo
    {
        return $this->belongsTo(
            WtfOperation::class,
            'operation_id',
        );
    }

    public function taskGroup(): BelongsTo
    {
        return $this->belongsTo(
            TaskGroup::class,
            'task_group_id',
        );
    }   
    
    public function taskItemGroup(): BelongsTo
    {
        return $this->belongsTo(
            TaskItemGroup::class,
            'task_item_group_id',
        );
    }     

    public function scopeBySubjectType(Builder $query, string $type)
    {
        $query->where('subject_type', '=', $type);
    }
}
