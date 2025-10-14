<?php

namespace App\Models;

use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionSubject extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'inspection_id',
        'subject_id',
        'subject_type',
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'inspection_assignments';
    }
    
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, "ticket_id");
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
