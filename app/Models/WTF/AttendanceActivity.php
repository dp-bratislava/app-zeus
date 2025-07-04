<?php

namespace App\Models\WTF;

use App\Models\Attendance\Attendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceActivity extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_attendance_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'activity_id',
        'time_from',
        'time_to',
        'duration',
        'is_fulfilled',
        'atendance_id',
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
            // 'time_from' => 'time',
            // 'time_to' => 'time',
        ];
    }  

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, "task_id");
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, "activity_id");
    }

    public function standarisedActivity(): BelongsTo
    {
        return $this->belongsTo(StandardisedActivity::class, "standardised_activity_id");
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, "attendance_id");
    }
}
