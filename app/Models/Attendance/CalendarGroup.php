<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarGroup extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_att_calendar_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
    ];

    // public function activity(): BelongsTo
    // {
    //     return $this->belongsTo(Activity::class, "activity_id");
    // }
}
