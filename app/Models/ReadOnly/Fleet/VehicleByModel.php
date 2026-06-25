<?php

namespace App\Models\ReadOnly\Fleet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VehicleByModel extends Model
{
    protected $table = 'vehicle_by_model';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'model';

    protected $keyType = 'string';

    public static function query()
    {
        $prefix = config('pkg-fleet.table_prefix', 'fleet_');

        $subQuery = DB::table($prefix.'vehicles as v')
            ->join($prefix.'vehicle_models as vm', 'vm.id', '=', 'v.model_id')
            ->selectRaw('vm.title as model')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN v.state = 'under-repair' THEN 1 ELSE 0 END) as `under-repair`")
            ->groupBy('vm.title');

        return (new static)->newModelQuery()
            ->fromSub($subQuery, (new static)->getTable());
    }
}
