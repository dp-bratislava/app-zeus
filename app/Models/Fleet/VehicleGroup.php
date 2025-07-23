<?php

namespace App\Models\Fleet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleGroup extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_vehicle_groups';
}
