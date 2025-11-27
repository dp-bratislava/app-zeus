<?php

namespace App\Models\ReadOnly\Reports;

use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class VehicleStatusReport extends Model
{

    protected $primaryKey = 'vehicle_id';
    public $incrementing = false;
    public $timestamps = false; // views usually don’t have timestamps

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [];
    protected $casts = [
        'date_from' => 'date'
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'vw_vehicle_status_report';
    }


    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getOutOfserviceDaysAttribute()
    {
        return floor($this->date_from?->diffInDays());
    }
}
