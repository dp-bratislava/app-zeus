<?php

namespace App\Models\DPB;

use App\Models\Datahub\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentGroup extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_department_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
        'description',
    ];

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            "dpb_department_group",
            "department_id",
            "department_group_id",
        );
    }
}
