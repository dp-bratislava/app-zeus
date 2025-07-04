<?php

namespace App\Models\Attendance;

use App\Models\Datahub\EmployeeContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_att_attendance';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'calendar_group_id',
        'employee_contract_id',
        'shift_start',
        'shift_duration',
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
            // 'time_from' => 'time',
            // 'time_to' => 'time',
        ];
    }  

    public function calendarGroup(): BelongsTo
    {
        return $this->belongsTo(CalendarGroup::class, "calendar_group_id");
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(EmployeeContract::class, "employee_contract_id");
    }
}
