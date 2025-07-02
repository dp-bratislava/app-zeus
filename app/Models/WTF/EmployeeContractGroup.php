<?php

namespace App\Models\WTF;

use App\Models\Datahub\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeContractGroup extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_employee_contract_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'department_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, "department_id");
    }
}
