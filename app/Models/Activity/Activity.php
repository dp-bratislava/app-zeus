<?php

namespace App\Models\Activity;

use App\Models\TS\Ticket;
use Dpb\Packages\Activities\Models\Activity as BaseActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends BaseActivity
{
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, "ticket_id");
    }

    public function workAssignments(): HasMany
    {
        return $this->hasMany(
            ActivityWorkAssignment::class,
            'activity_id',
        );
    }
}
