<?php

namespace App\Models\Fleet\Inspection;

use App\Models\Fleet\ServiceGroup;
use App\Models\Fleet\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_inspections';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date_planned_for',
        'date_start',
        'date_end',
        'vehicle_id',
        'inspection_template_id',
        'service_group_id',
        'status_id',
        'distance_traveled',
        'note',
        'failures',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_planned_for' => 'date',
            'date_start' => 'date',
            'date_end' => 'date',
        ];
    } 

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, "vehicle_id");
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, "status_id");
    }

    public function serviceGroup(): BelongsTo
    {
        return $this->belongsTo(ServiceGroup::class, "service_group_id");
    }

    public function inspectionTemplate(): BelongsTo
    {
        return $this->belongsTo(InspectionTemplate::class, "inspection_template_id");
    }
}
