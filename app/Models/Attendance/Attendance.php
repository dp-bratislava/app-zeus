<?php

namespace App\Models\Attendance;

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

    public function group(): BelongsTo
    {
        return $this->belongsTo(CalendarGroup::class, "calendar_group_id");
    }

    // public function contract(): BelongsTo
    // {
    //     return $this->belongsTo(Empl::class, "calendar_group_id");
    // }
}
