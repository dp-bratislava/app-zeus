<?php

namespace App\Models\TS\Task;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_task_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, "task_template_id");
    }

    public function templateGroup(): BelongsTo
    {
        return $this->belongsTo(TemplateGroup::class, "template_group_id");
    }
}
