<?php

namespace App\Registries\Snapshots;

class HRContractSnapshotSQLRegistry
{
    protected function targetTable(): string
    {
        return 'mvw_hr_contract_snapshots';
    }

    public function build(): string
    {
        return "
            INSERT INTO {$this->targetTable()}
            ({$this->columns()})
            SELECT
                {$this->select()}
            {$this->from()}
            {$this->joins()}
            ON DUPLICATE KEY UPDATE
                updated_at = VALUES(updated_at)            
        ";
    }

    protected function map(): array
    {
        return [
            // contract
            'contract_id' => 'c.id',

            // employee
            'hash' => 'e.hash',
            'first_name' => 'e.first_name',
            'last_name' => 'e.last_name',
            'gender' => 'e.gender',

            // department
            'department_code' => 'd.code',
            'department_title' => 'd.title',

            // profession
            'profession_code' => 'p.code',
            'profession_title' => 'p.title',

            // contract type
            'contract_type_uri' => 'ct.uri',
            'contract_type_title' => 'ct.title',

            // employee circuit
            'employee_circuit_code' => 'ec.code',

            // contract fields
            'pid' => 'c.pid',
            'is_active' => 'c.is_active',
            'is_primary' => 'c.is_primary',

            // transformed field
            'valid_from' => "
            CASE
                WHEN c.valid_from REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'
                THEN STR_TO_DATE(c.valid_from, '%Y-%m-%d')
                ELSE NULL
            END
        ",

            'valid_to' => 'c.valid_to',

            'created_at' => 'c.created_at',
            'updated_at' => 'c.updated_at',
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

    protected function joins(): string
    {
        return "
            JOIN tmp_contract_ids tmp ON tmp.id = c.id
            LEFT JOIN datahub_employees e ON e.id = c.datahub_employee_id
            LEFT JOIN datahub_departments d ON d.id = c.datahub_department_id
            LEFT JOIN datahub_professions p ON p.id = c.datahub_profession_id
            LEFT JOIN datahub_employee_circuits ec ON ec.id = c.circuit_id
            LEFT JOIN datahub_contract_types ct ON ct.id = c.datahub_contract_type_id

        ";
    }

    protected function from(): string
    {
        return "
            FROM datahub_employee_contracts c
        ";
    }
}
