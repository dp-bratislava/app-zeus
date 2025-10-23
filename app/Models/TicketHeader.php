<?php

namespace App\Models;

use App\Models\Datahub\Department;
use App\Models\Datahub\EmployeeContract;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketHeader extends Model
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
        'assigned_to',
        'source_id',
        'source_type',
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'ticket_headers';
    }
        
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, "ticket_id");
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, "department_id");
    }

    public function author(): BelongsTo
    {
        // return $this->belongsTo(User::class, "author_id");
        return $this->belongsTo(EmployeeContract::class, "author_id");
    }

    public function assignedTo(): BelongsTo
    {
        // return $this->belongsTo(User::class, "assigned_to");
        return $this->belongsTo(EmployeeContract::class, "assigned_to");
    }
    
    public function source(): MorphTo
    {
        return $this->morphTo();
    }    
}
