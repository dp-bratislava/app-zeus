<?php

namespace App\Models\ReadOnly\Fleet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VehicleByMaintenanceGroup extends Model
{
    protected $table = 'vehicle_by_maintenance_group';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'maintenance_group';

    protected $keyType = 'string';

    public static function query()
    {
        $prefix = config('pkg-fleet.table_prefix', 'fleet_');

        $subQuery = DB::table($prefix.'vehicles as v')
            ->leftJoin($prefix.'maintenance_groups as mg', 'mg.id', '=', 'v.maintenance_group_id')
            ->selectRaw("COALESCE(mg.title, '—') as maintenance_group")
            ->selectRaw("SUM(CASE WHEN v.state = 'under-repair' THEN 1 ELSE 0 END) as `under-repair`")
            ->selectRaw("SUM(CASE WHEN v.state = 'warranty-claim' THEN 1 ELSE 0 END) as `warranty-claim`")
            ->selectRaw("SUM(CASE WHEN v.state = 'warranty-repair' THEN 1 ELSE 0 END) as `warranty-repair`")
            ->selectRaw("SUM(CASE WHEN v.state = 'missing-parts' THEN 1 ELSE 0 END) as `missing-parts`")
            ->groupBy('mg.title');

        return (new static)->newModelQuery()
            ->fromSub($subQuery, (new static)->getTable());
    }
}
