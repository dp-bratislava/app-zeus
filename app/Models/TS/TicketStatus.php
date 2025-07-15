<?php

namespace App\Models\Ts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketStatus extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_ticket_statuses';

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
        return $this->hasMany(Ticket::class, "status_id");
    }
}
