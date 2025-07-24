<?php

namespace App\Models\Fleet\Tire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parameter extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_tire_parameters';

    protected $guarded = [];

    public function constructionType(): BelongsTo
    {
        return $this->belongsTo(ConstructionType::class, "construction_type_id");
    }
}
