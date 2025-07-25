<?php

namespace App\Models\TS\Task;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateGroup extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_task_template_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'title',
        'description',
        'department_id',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, "template_group_id");
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TemplateGroup::class, "parent_id");
    }
}
