<?php

namespace Dpb\Modules\Tasks\Enums;

/**
 * Based on place of origin code
 */
enum TaskPlaceOfOrigin: string
{
    case Maintenance = 'during-maintenance';
    case InService = 'in-service';
    case InServiceDispatch = 'in-service-dispatch';
    case PlannedMaintenance = 'planned-maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Maintenance => 'Pri pravidelnej kontrole',
            self::InService => 'V prevádzke dispečing',
            self::InServiceDispatch => 'Dispečing',
            self::PlannedMaintenance => 'Plánovaná kontrola',
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