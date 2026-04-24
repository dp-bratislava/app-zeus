<?php

namespace App\Registries\Snapshots;

class WorkTaskSubjectSnapshotSQLRegistry
{
    protected function targetTable(): string
    {
        return 'mvw_work_task_subject_snapshots';
    }

    protected function resolvedSubjectTypeCTE(): string
    {
        return "
            subject_base AS (
                SELECT
                    t.id AS task_id,
                    CASE
                        WHEN t.maintainable_type = 'Dpb\\\\WorkTimeFund\\\\Models\\\\Maintainables\\\\Vehicle' THEN 'vehicle'
                        WHEN t.maintainable_type = 'Dpb\\\\WorkTimeFund\\\\Models\\\\Maintainables\\\\Table' THEN 'table'
                        ELSE NULL
                    END AS type,
                    t.maintainable_id AS entity_id,
                    t.updated_at as updated_at
                FROM dpb_worktimefund_model_task t
                    JOIN tmp_wtf_task_ids tmp ON tmp.id = t.id
            ),
            subject_resolved AS (
                SELECT
                    sb.task_id,
                    COALESCE(sb.type, at.title) as type,
                    COALESCE(vr.label, tr.label, ao.label) AS label,
                    sb.updated_at
                FROM subject_base sb
                    LEFT JOIN vehicle_resolved vr ON vr.vehicle_id = sb.entity_id AND sb.type = 'vehicle'
                    LEFT JOIN table_resolved tr ON tr.device_id = sb.entity_id AND sb.type = 'table'
                    LEFT JOIN dpb_worktimefund_mm_morphable_attributeoption mao ON mao.morphable_id = sb.task_id AND mao.morphable_type = 'Dpb\\\\WorkTimeFund\\\\Models\\\\Task'
                    LEFT JOIN dpb_worktimefund_model_attributeoption ao ON ao.id = mao.attributeoption_id
                    LEFT JOIN dpb_worktimefund_model_attributetype at ON at.id = ao.attributetype_id                
            )
        ";
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

    protected function vehicleResolvedCTE(string $uri = 'vehicle_resolved'): string
    {
        return "
            latest_code AS (
                SELECT vehicle_id, vehicle_code_id
                FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY date_from DESC) rn
                    FROM fleet_vehicle_code_history
                ) x
                WHERE rn = 1
            ),
            latest_plate AS (
                SELECT vehicle_id, licence_plate_id
                FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY date_from DESC) rn
                    FROM fleet_licence_plate_history
                ) x
                WHERE rn = 1
            ),
            vehicle_resolved AS (
                SELECT
                    v.id AS vehicle_id,
                    COALESCE(vc.code, lp.code) AS label
                FROM fleet_vehicles v
                    LEFT JOIN latest_code lc ON lc.vehicle_id = v.id
                    LEFT JOIN fleet_vehicle_codes vc ON vc.id = lc.vehicle_code_id
                    LEFT JOIN latest_plate lp_ref ON lp_ref.vehicle_id = v.id
                    LEFT JOIN fleet_licence_plates lp ON lp.id = lp_ref.licence_plate_id
            )        
        ";
    }

    protected function resolvedTableCTE(): string
    {
        return "
            latest_location AS (
                SELECT device_id, location_id
                FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY device_id ORDER BY date_from DESC) rn
                    FROM dvc_device_location_history
                ) x
                WHERE rn = 1
            ),
            table_resolved AS (
                SELECT
                    d.id AS device_id,
                    dl.title AS label
                FROM dvc_devices d
                    LEFT JOIN latest_location ll ON ll.device_id = d.id
                    LEFT JOIN dvc_device_locations dl ON dl.id = ll.location_id
            )        
        ";
    }

    protected function with(): string
    {
        return "
            {$this->vehicleResolvedCTE()},
            {$this->resolvedTableCTE()},
            {$this->resolvedSubjectTypeCTE()}
        ";
    }

    public function build(): string
    {
        return "
            INSERT INTO {$this->targetTable()}
            ({$this->columns()})
            WITH {$this->with()}        
            SELECT
                {$this->select()}
            {$this->from()}
            WHERE sr.type IS NOT NULL
            
            ON DUPLICATE KEY UPDATE
                updated_at = VALUES(updated_at)            
        ";
    }

    protected function map(): array
    {
        return [
            'wtf_task_id' => 'sr.task_id',
            'subject_type' => 'sr.type',
            'subject_label' => 'sr.label',
            'updated_at' => 'sr.updated_at',
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

    protected function from(): string
    {
        return "
            FROM subject_resolved sr
        ";
    }
}
