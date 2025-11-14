<?php

namespace App\Models;

use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Inspections\Models\Inspection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class InspectionAssignment extends Model
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

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public static function scopeByVehicleType(Builder $query, string|array $type)
    {
        $query->hasMorph('subject', app(Vehicle::class)->getMorphClass())
            ->whereHas('subject', function ($q) use ($type) {
                $q->byType($type);
            });
    }

    public static function scopeBySubjectCode(Builder $query, string|array $subjectMorphClasses, string|array $codes)
    {
        $codes = Arr::wrap($codes);
        $subjectMorphClasses = Arr::wrap($subjectMorphClasses);
        
        $query->hasMorph('subject', $subjectMorphClasses)
            ->whereHas('subject', function ($q) use ($codes) {
                $q->byMaintenanceGroup($codes);
            });
    }    
}
