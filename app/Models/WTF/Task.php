<?php

namespace App\Models\WTF;

use App\Models\Datahub\Department;
use App\Models\WTF\TaskSubject\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'date',
        'department_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, "parent_id");
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, "department_id");
    }

    public function standardisedActivities(): HasMany
    {
        return $this->hasMany(StandardisedActivity::class, "task_id");
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, "task_id");
    }

    public function materials(): HasMany
    {
        return $this->hasMany(TaskMaterial::class, "task_id");
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, "status_id");
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TaskPriority::class, "priority_id");
    }

    public function vehicles(): MorphToMany
    {
        return $this->morphedByMany(
            Vehicle::class,
            'subject',
            'dpb_wtf_task_subjects',
            'subject_id',
            'task_id'
        );
    }
}
