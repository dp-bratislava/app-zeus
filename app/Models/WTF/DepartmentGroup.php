<?php

namespace App\Models\WTF;

use App\Models\Datahub\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentGroup extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_department_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
    ];

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class);
    }    
}
