<?php

namespace App\Models;

use App\Models\Datahub\EmployeeContract;
use Dpb\Packages\WorkLog\Models\WorkInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkAssignment extends Model
{
    public $guarded = [];

    public function getTable(): string
    {
        return config('database.table_prefix') . 'work_assignments';
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
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
