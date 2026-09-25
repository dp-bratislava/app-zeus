<?php

namespace Dpb\Modules\Tasks\Resolvers;

use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\DepartmentGroup;

class DepartmentGroupResolver
{
    public function __construct(
        private DepartmentService $departmentSvc
    ) {}

    public function resolve(): ?DepartmentGroup
    {
        $department = $this->departmentSvc->getActiveDepartment();

        return match ($department?->code) {
            // mainteannce group trams
            '5400', '5621', '5622',
            // mainteannce group troleybus
            '5521', '5522',
            // mainteannce group bus
            '7213', '7223', '7233'
            => DepartmentGroup::MaintenanceGroup,

            // gumari
            '7130' => DepartmentGroup::TireManagement,
            // vehicle cleaning
            '9486' => DepartmentGroup::VehicleCleaning,

            // generic departments
            default => null,
        };
    }
}