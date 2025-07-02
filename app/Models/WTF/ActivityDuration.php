<?php

namespace App\Models\WTF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityDuration extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_activity_durations';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'activity_id',
        'duration',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, "activity_id");
    }
}
