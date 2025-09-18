<?php

namespace App\Models;

use Dpb\Package\Activities\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityAssignment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'activity_id',
        'subject_id',
        'subject_type',
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'activity_assignments';
    }

    public function activity(): BelongsTo 
    {
        return $this->belongsTo(Activity::class);
    } 

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }    
}
