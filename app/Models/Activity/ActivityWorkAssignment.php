<?php

namespace App\Models\Activity;

use App\Models\Datahub\EmployeeContract;
use App\Models\Activity\Activity;
use Dpb\Packages\WorkLog\Models\WorkInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityWorkAssignment extends Model
{
    public $guarded = [];

    public function getTable(): string
    {
        return config('database.table_prefix') . 'activity_work_assignments';
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, "activity_id");
    }

    public function employeeContract(): BelongsTo
    {
        return $this->belongsTo(EmployeeContract::class, "employee_contract_id");
    }

    public function workInterval(): BelongsTo
    {
        return $this->belongsTo(WorkInterval::class, "work_interval_id");
    }
}
