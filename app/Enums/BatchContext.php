<?php

namespace App\Enums;

/**
 * Based on place of origin code
 */
enum BatchContext: string
{
    case TaskBatch = 'tms-task-batch';

    public function label(): string
    {
        return match ($this) {
            self::TaskBatch => 'Hromadné zákazky',
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