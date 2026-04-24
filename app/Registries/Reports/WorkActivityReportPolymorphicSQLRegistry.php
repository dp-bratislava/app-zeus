<?php

namespace App\Registries\Reports;

class WorkActivityReportPolymorphicSQLRegistry
{
    protected function resolvedSubjectTypeCTE(): string
    {
        return "
        ";
    }

    protected function resolvedVehicleCTE(): string
    {
        return "
            WITH latest_code AS (
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
            WITH latest_location AS (
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

    protected function resolvedCustomCTE(): string
    {
        return "
       
        ";
    }


    public function build()
    {
        return "
            INSERT INTO mvw_work_activity_report (
                task_item_id,
                task_assigned_to_label,
                updated_at
            )
            WITH latest_code AS (...),
                latest_plate AS (...),
                vehicle_resolved AS (...)

            SELECT
                tia.task_item_id,
                vr.label,
                NOW()
            FROM tms_task_item_assignments tia
            LEFT JOIN tsk_tasks t ON t.id = tia.task_id
            LEFT JOIN vehicle_resolved vr
                ON vr.vehicle_id = t.maintainable_id
            WHERE tia.task_item_id IN (...)
            ON DUPLICATE KEY UPDATE
                task_assigned_to_label = VALUES(task_assigned_to_label),
                updated_at = VALUES(updated_at)
    ";
    }
}
