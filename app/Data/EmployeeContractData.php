<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class EmployeeContractData extends Data
{
    public function __construct(
        public int $id,
        public ?string $pid,
        public ?DepartmentData $department,
        public ?EmployeeData $employee,
    ) {}
}
