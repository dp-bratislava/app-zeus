<?php

namespace App\Models\TS;

use App\Models\Activity\Activity;
use App\Models\Datahub\Department;
use App\States\Ticket\TicketState;
use App\Traits\HasStateHistory;
use Dpb\Packages\Tickets\Models\Ticket as BaseTicket;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\ModelStates\HasStates;
use Spatie\ModelStates\HasStatesContract;

class Ticket extends BaseTicket implements HasStatesContract
{
    use HasStates;
    use HasStateHistory;

    protected $casts = [
        'state' => TicketState::class,
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->mergeFillable(['subject_id', 'subject_type']);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, "department_id");
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, "ticket_id");
    }

    // public function materials(): HasMany
    // {
    //     return $this->hasMany(Material::class, "ticket_id");
    // }


    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
