<?php

namespace App\Snapshots\Services;

use App\Snapshots\Core\Contracts\SnapshotContract;
use App\Snapshots\Core\Contracts\SnapshotExecutionStrategy;
use App\Snapshots\Core\SnapshotExecutionState;
use App\Snapshots\Core\SnapshotRunContext;
use App\Snapshots\Strategies\TempTableStrategy;
use Illuminate\Support\Facades\DB;

class FleetVehicleSnapshot implements SnapshotContract
{
    public function getKey(): string
    {
        return 'fleet-vehicle';
    }

    public function targetTable(): string
    {
        return 'mvw_fleet_vehicle_snapshots';
    }

    public function sourceTable(): string
    {
        return 'fleet_vehicles';
    }

    public function strategy(): SnapshotExecutionStrategy
    {
        return app(TempTableStrategy::class);
    }

    public function idQuery(SnapshotRunContext $context)
    {
        return DB::table($this->sourceTable())
            ->where('updated_at', '>', $context->from)
            ->orWhere('deleted_at', '>', $context->from)
            ->select('id');
    }

    public function run(SnapshotRunContext $context, SnapshotExecutionState $state): void
    {
        DB::statement(
            $this->buildInsertSql(
                $state->tempTable,
            )
        );
    }

    public function vehicleLatestCodeCTE(string $uri = 'latest_code'): string
    {
        return "
            {$uri} AS (
                SELECT vehicle_id, vehicle_code_id
                FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY date_from DESC) rn
                    FROM fleet_vehicle_code_history
                ) x
                WHERE rn = 1
            )
        ";
    }

    public function vehicleLatestPlateCTE(string $uri = 'latest_plate'): string
    {
        return "
            {$uri} AS (
                SELECT vehicle_id, licence_plate_id
                FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY date_from DESC) rn
                    FROM fleet_licence_plate_history
                ) x
                WHERE rn = 1
            )
        ";
    }

    protected function with(string $tempTable): string
    {
        return "
            {$this->vehicleLatestCodeCTE()},
            {$this->vehicleLatestPlateCTE()}
        ";
    }

    public function buildInsertSql(string $tempTable): string
    {
        return "
            INSERT INTO {$this->targetTable()}
            ({$this->columns()})
            WITH {$this->with($tempTable)}   
            SELECT
                {$this->select()}
            {$this->from()}
            {$this->joins($tempTable)}
            ON DUPLICATE KEY UPDATE
                updated_at = VALUES(updated_at)            
        ";
    }

    protected function map(): array
    {
        return [
            'vehicle_id' => 'v.id',
            'code' => 'vc.`code`',
            'licence_plate' => 'lp.`code`',
            'model' => 'vm.title',
            'type' => 'vt.title',
            'updated_at' => 'v.updated_at',
        ];
    }

    protected function columns(): string
    {
        return implode(',', array_keys($this->map()));
    }

    protected function select(): string
    {
        return implode(",\n", $this->map());
    }

    protected function joins(string $tempTable): string
    {
        return "
            JOIN {$tempTable} tmp ON tmp.id = v.id
            LEFT JOIN fleet_vehicle_models vm ON vm.id = v.model_id
            LEFT JOIN fleet_vehicle_types vt ON vt.id = vm.type_id
            LEFT JOIN latest_code lc ON lc.vehicle_id = v.id
            LEFT JOIN fleet_vehicle_codes vc ON vc.id = lc.vehicle_code_id
            LEFT JOIN latest_plate lp_ref ON lp_ref.vehicle_id = v.id
            LEFT JOIN fleet_licence_plates lp ON lp.id = lp_ref.licence_plate_id
        ";
    }

    protected function from(): string
    {
        return "
            FROM fleet_vehicles v
        ";
    }
}
