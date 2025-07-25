<?php

namespace App\Models\TS\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketMaterial extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_ticket_materials';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'title',
        'unit_price',
        'vat',
        'quantity',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, "ticket_id");
    }
}
