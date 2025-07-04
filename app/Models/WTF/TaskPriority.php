<?php

namespace App\Models\WTF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskPriority extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_task_priorities';

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
        return $this->hasMany(Task::class, "priority_id");
    }
}
