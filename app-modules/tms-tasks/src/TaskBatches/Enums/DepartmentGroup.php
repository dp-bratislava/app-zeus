<?php

namespace Dpb\Modules\Tasks\Enums;

enum DepartmentGroup: string
{
    case MaintenanceGroup = 'maintenance-group';    
    case TireManagement = 'tire-management';    
    case VehicleCleaning = 'vehicle-cleaning';    
    
    public function label(): string
    {
        return match ($this) {
            self::MaintenanceGroup => 'MaintenanceGroup',
            self::TireManagement => 'TireManagement',
            self::VehicleCleaning => 'VehicleCleaning',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => $case->label(),
            ])
            ->toArray();
    }       
}