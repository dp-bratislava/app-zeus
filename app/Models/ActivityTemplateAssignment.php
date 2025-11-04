<?php

namespace App\Models;

use Dpb\Package\Activities\Models\ActivityTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityTemplateAssignment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'template_id',
        'subject_id',
        'subject_type',
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'activity_templatables';
    }

    public function template(): BelongsTo 
    {
        return $this->belongsTo(ActivityTemplate::class);
    } 

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }    
}
