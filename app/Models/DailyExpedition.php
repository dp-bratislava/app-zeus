<?php

namespace App\Models;

use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class DailyExpedition extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'vehicle_id',
        'state',
        'service',
        'note',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'daily_expeditions';
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getOutOfserviceFromAttribute(): ?Carbon
    {
        // $vehicleId = $this->vehicle->id;
        // $dailyExpedition = $this->query()->whereHas('vehicle', function($q) use ($vehicleId) {
        //     $q->where('id', '=', $vehicleId);
        // })
        // // ->get();
        // ->where('state', '=', 'out-of-service')
        // ->orderBy('date', 'desc')
        // ->first();

        // // $result = $dailyExpedition?->date;
        // print_r($vehicleId);
        // print_r( $dailyExpedition?->date);
        // return null;
        // // return $result;
        return Carbon::parse('2025-11-05');
    }

    public function getOutOfserviceDaysAttribute()
    {
        return floor($this->getOutOfserviceFromAttribute()?->diffInDays());
    }
}
