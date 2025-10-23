<?php

namespace App\Models;

use App\Models\Datahub\EmployeeContract;
use Dpb\Package\Activities\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityWorkAssignment extends Model
{
    public $guarded = [];

    public function getTable(): string
    {
        return config('database.table_prefix') . 'activity_work_assignments';
    }

    public function employeeContract(): BelongsTo
    {
        return $this->belongsTo(EmployeeContract::class, "employee_contract_id");
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, "activity_id");
    }
}
