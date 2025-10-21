<?php

namespace App\Models;

use Dpb\Package\Inspections\Models\InspectionTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionTemplateAssignment extends Model
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
        return config('database.table_prefix') . 'inspection_templatables';
    }

    public function template(): BelongsTo 
    {
        return $this->belongsTo(InspectionTemplate::class);
    } 

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }    
}
