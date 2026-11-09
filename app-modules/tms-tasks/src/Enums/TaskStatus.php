<?php

namespace Dpb\Modules\Tasks\Enums;

enum TaskStatus: string
{
    case Created = 'created';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Nová',
            self::Closed => 'Ukončená',
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