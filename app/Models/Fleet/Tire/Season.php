<?php

namespace App\Models\Fleet\Tire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Season extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_tire_seasons';
}
