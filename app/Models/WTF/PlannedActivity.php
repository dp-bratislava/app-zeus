<?php

namespace App\Models\WTF;

use App\Models\Datahub\EmployeeContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlannedActivity extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_planned_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'template_id',
        'duration',
        'employee_contract_id',
        'status_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    } 

    public function template(): BelongsTo
    {
        return $this->belongsTo(ActivityTemplate::class, "template_id");
    }

    public function employeeContract(): BelongsTo
    {
        return $this->belongsTo(EmployeeContract::class, "employee_contract_id");
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ActivityStatus::class, "status_id");
    }
}
