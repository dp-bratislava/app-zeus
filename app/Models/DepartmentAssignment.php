<?php

namespace App\Models;

use App\Models\Datahub\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentAssignment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'department_id',
        'subject_id',
        'subject_type',
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'department_assignments';
    }

    public function department(): BelongsTo 
    {
        return $this->belongsTo(Department::class);
    } 

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }    
}
