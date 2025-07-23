<?php

namespace App\Models\Fleet\Tire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConstructionType extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_tire_construction_types';
}
