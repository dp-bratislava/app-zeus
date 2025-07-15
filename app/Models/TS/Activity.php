<?php

namespace App\Models\TS;

use App\Models\Datahub\EmployeeContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'ticket_id',
        'time_from',
        'time_to',
        'description',
        'employee_contract_id',
        'status_id',
        'standardised_activity_id',
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
            'time_from' => 'datetime',
            'time_to' => 'datetime',
        ];
    } 

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, "ticket_id");
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ActivityStatus::class, "status_id");
    }

    public function standardisedActivity(): BelongsTo
    {
        return $this->belongsTo(StandardisedActivity::class, "standardised_activity_id");
    }

    public function employeeContract(): BelongsTo    
    {
        return $this->belongsTo(EmployeeContract::class, "employee_contract_id");
    }    
}
