<?php

namespace App\Models\Fleet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleType extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_vehicle_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
    ];

    public function model(): HasMany
    {
        return $this->hasMany(VehicleModel::class, "type_id");
    }    
}
