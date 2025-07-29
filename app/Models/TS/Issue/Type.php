<?php

namespace App\Models\TS\Issue;

use App\Models\DPB\DepartmentGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_issue_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
        'description',
        'department_group_id',
        'parent_id',
    ];

    // public function tasks(): HasMany
    // {
    //     return $this->hasMany(Task::class, "template_group_id");
    // }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Type::class, "parent_id");
    }

    public function departmentGroup(): BelongsTo
    {
        return $this->belongsTo(DepartmentGroup::class, "department_group_id");
    }
}
