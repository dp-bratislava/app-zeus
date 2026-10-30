<?php

namespace Dpb\Modules\Tasks\TaskBatches\Models;

use Illuminate\Database\Eloquent\Model;

class TaskBatchGroup extends Model
{
    // use SoftDeletes;
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
    ];


    public function getTable()
    {
        return config('pkg-task-ms.table_prefix') . 'task_batch_groups';
    }
}
