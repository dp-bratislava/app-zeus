<?php

namespace App\Models\Fleet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleModel extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_vehicle_models';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'length',
        'warranty',
        'type_id',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, "type_id");
    }    
}
