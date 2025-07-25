<?php

namespace App\Models\TS\Task;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskStatus extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_task_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, "status_id");
    }
}
