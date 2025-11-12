<?php

namespace App\Models;

use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketItemAssignment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_item_id',
        'assigned_to_id',
        'assigned_to_type',
        'author_id',
        'supervised_by',
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'ticket_item_assignments';
    }

    public function ticket(): HasOneThrough
    {
        return $this->hasOneThrough(
            Ticket::class,   // final model you want
            TicketItem::class,   // intermediate model
            'id',  // foreign key on intermediate model (users.country_id)
            'ticket_id',     // foreign key on final model (posts.user_id)
            'ticket_id',          // local key on this model (countries.id)
            'id'           // local key on intermediate model (users.id)
        );
    }

    public function ticketItem(): BelongsTo
    {
        return $this->belongsTo(TicketItem::class, "ticket_item_id");
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, "author_id");
        // return $this->belongsTo(EmployeeContract::class, "author_id");
    }

    public function supervisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "supervised_by");
        // return $this->belongsTo(EmployeeContract::class, "assigned_to");
    }

    public function assignedTo(): MorphTo
    {
        return $this->morphTo();
    }
}
