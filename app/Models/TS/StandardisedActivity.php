<?php

namespace App\Models\TS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardisedActivity extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_standardised_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'template_id',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, "ticket_id");
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(StandardisedActivityTemplate::class, "template_id");
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, "standardised_activity_id");
    }  
}
