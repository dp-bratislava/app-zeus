<?php

namespace Dpb\Modules\Tasks\TaskBatches\Models;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Read only model for db view
 */
class DepartmentTaskItemGroupView extends Model
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

    public function getTable()
    {
        return 'vw_' . config('pkg-task-ms.table_prefix') . 'task_batch_department_task_item_groups';
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
}
