<?php

namespace Dpb\Modules\Tasks\Enums;

enum TaskSubjectType: string
{
    case Vehicle = 'vehicle';

    public function label(): string
    {
        return match ($this) {
            self::Vehicle => 'Vozidlo'
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