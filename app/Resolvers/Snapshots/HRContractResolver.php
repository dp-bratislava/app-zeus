<?php

namespace App\Resolvers\Snapshots;

class HRContractResolver
{
    public function resolve($contract): array
    {
        return [
            'contract_id' => $contract->id,
            // employee 
            'hash' => $contract->employee?->hash,
            'first_name' => $contract->employee?->first_name,
            'last_name' => $contract->employee?->last_name,
            'gender' => $contract->employee?->gender,

            // department
            'department_code' => $contract->department?->code,
            'department_title' => $contract->department?->title,

            // profession
            'profession_code' => $contract->profession?->code,
            'profession_title' => $contract->profession?->title,

            // contract type 
            'contract_type_uri' => $contract->type?->uri,
            'contract_type_title' => $contract->type?->title,

            // employee circuit
            'employee_circuit_code' => $contract->circuit?->code,

            // contract
            'pid' => $contract->pid,
            'is_active' => $contract->is_active,
            'is_primary' => $contract->is_primary,
            'valid_from' => $this->normalizeDate($contract->valid_from),
            'valid_to' => $contract->valid_to,

            'updated_at' => now(),
        ];
    }

    public function batchResolve($contracts)
    {
        $result = [];

        if (empty($contracts)) {
            return $result;
        }

        foreach ($contracts as $contract) {
            $result[] = $this->resolve($contract);
        }

        return $result;
    }

    function normalizeDate($value): ?string
    {
        try {
            $date = \Carbon\Carbon::parse($value);

            // MySQL valid DATE range check
            if ($date->year < 1000 || $date->year > 9999) {
                return null;
            }

            return $date->toDateString(); // Y-m-d
        } catch (\Exception $e) {
            return null;
        }
    }
}
