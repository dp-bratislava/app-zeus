<?php

namespace App\Models\Activity;

use App\Models\Datahub\EmployeeContract;
use Dpb\Packages\Activities\Models\Activity;
use Dpb\Packages\WorkLog\Models\WorkInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityWorkAssignment extends Model
{
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
