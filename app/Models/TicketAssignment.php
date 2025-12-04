<?php

namespace App\Models;

use App\Models\Datahub\Department;
use App\Models\Datahub\EmployeeContract;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TicketAssignment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'department_id',
        'author_id',
        'assigned_to_id',
        'assigned_to_type',
        'source_id',
        'source_type',
        'subject_id',
        'subject_type',
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'ticket_assignments';
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, "ticket_id");
    }

    public function ticketItems(): HasManyThrough
    {
        return $this->hasManyThrough(
            TicketItem::class,
            Ticket::class,
            'id',          // Ticket primary key
            'ticket_id',   // TicketItem FK
            'ticket_id',   // TicketAssignment FK to Ticket
            // 'id'           // Ticket PK
        );
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, "department_id");
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, "author_id");
        // return $this->belongsTo(EmployeeContract::class, "author_id");
    }

    public function assignedTo(): MorphTo
    {
        return $this->morphTo();
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTitleAttribute(): string
    {
        $ticketGroupShort = Str::of($this->ticket->group->title)
            ->explode(' ')                 // split by space
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))  // first char uppercase
            ->implode('');
        $date = $this->ticket->date->format('ymd');
        $subject = $this->subject->code?->code;
        
        return join('-', [$ticketGroupShort, $date, $subject]);
    }
}
