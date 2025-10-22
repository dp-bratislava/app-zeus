<?php

namespace App\Models\ReadOnly\Fleet;

use Illuminate\Database\Eloquent\Model;

class VehicleByMaintenanceGroup extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [];
     public $timestamps = false; // views usually don’t have timestamps

    public function getTable()
    {
        return config('pkg-fleet.table_prefix') . 'vw_vehicle_by_maintenance_group';
    }
}
