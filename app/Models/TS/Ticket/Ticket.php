<?php

namespace App\Models\TS\Ticket;

use App\Models\BM\Building;
use App\Models\Datahub\Department;
use App\Models\Fleet\Vehicle;
use App\Models\TS\Task\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'date',
        'deadline',
        'department_id',
        'priority_id',
        'status_id',
        'group_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, "parent_id");
    }
    
    public function group(): BelongsTo
    {
        return $this->belongsTo(TicketGroup::class, "group_id");
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, "department_id");
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, "ticket_id");
    }

    public function materials(): HasMany
    {
        return $this->hasMany(TicketMaterial::class, "ticket_id");
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, "status_id");
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, "priority_id");
    }

    public function vehicles(): MorphToMany
    {
        return $this->morphedByMany(
            Vehicle::class,
            'subject',
            'dpb_ts_ticket_subjects',
            'ticket_id',
            'subject_id'
        );
    }

    public function buildings(): MorphToMany
    {
        return $this->morphedByMany(
            Building::class,
            'subject',
            'dpb_ts_ticket_subjects',
            'ticket_id',
            'subject_id'
        );
    }    
}
