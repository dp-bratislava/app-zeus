<?php

namespace App\Models\Fleet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleStatus extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_vehicle_statuses';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
    ];

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(
            Vehicle::class,
            "dpb_fleet_vehicle_status",
            "status_id",
            "vehicle_id",
        )
        ->withPivot('date')
        ->withTimestamps();
    } 
}
