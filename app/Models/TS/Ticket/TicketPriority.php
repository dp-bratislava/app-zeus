<?php

namespace App\Models\TS\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketPriority extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_ticket_priorities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, "priority_id");
    }
}
