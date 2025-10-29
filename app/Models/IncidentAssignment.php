<?php

namespace App\Models;

use Dpb\Package\Incidents\Models\Incident;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentAssignment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'incident_id',
        'author_id',
        'subject_id',
        'subject_type',
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'incident_assignments';
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, "incident_id");
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, "author_id");
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
