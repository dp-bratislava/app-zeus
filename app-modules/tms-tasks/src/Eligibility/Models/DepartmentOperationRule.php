<?php

namespace Dpb\Modules\Tasks\Eligibility\Models;

use Dpb\WorkTimeFund\Models\Operation as WtfOperation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentOperationRule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tbdgi_id', // task batch department group item
        'subject_type',
        'operation_id',
        'conditions',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];

    public function getTable()
    {
        return config('pkg-task-ms.table_prefix') . 'task_batch_department_operation_rules';
    }

    // public function taskItemGroup(): BelongsTo
    // {
    //     return $this->belongsTo(
    //         TaskItemGroup::class,
    //         'task_item_group_id'
    //     );
    // }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(
            WtfOperation::class,
            'operation_id',
        );
    }

    public function scopeBySubjectType(Builder $query, string $type)
    {
        $query->where('subject_type', '=', $type);
    }
}
