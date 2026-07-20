<?php

namespace App\Models\ReadOnly\Fleet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VehicleByState extends Model
{
    protected $table = 'vehicle_by_state';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'state';

    protected $keyType = 'string';

    public static function query()
    {
        $prefix = config('pkg-fleet.table_prefix', 'fleet_');

        $subQuery = DB::table($prefix.'vehicles')
            ->selectRaw('state, COUNT(*) as total')
            ->groupBy('state');

        return (new static)->newModelQuery()
            ->fromSub($subQuery, (new static)->getTable());
    }
}
