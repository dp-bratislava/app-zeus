<?php

namespace Dpb\Modules\Tasks\Enums;

/**
 * Based on task group codes
 */
enum TaskBatchContext: string
{
    // case DailyMaintenance = 'daily_maintenance';
    // case VehicleCleaningB = 'vehicle_cleaning_b';
    case DailyMaintenance = 'daily-maintenance';
    case VehicleCleaning = 'vehicle-cleaning';
    case VehicleCleaningB = 'vehicle-cleaning-b';

    public function label(): string
    {
        return match ($this) {
            self::DailyMaintenance => 'DO',
            self::VehicleCleaning => 'Čistenie Vozidiel',
            self::VehicleCleaningB => 'Čistenie B',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [
                $case->value => $case->label(),
            ])
            ->toArray();
    }
}