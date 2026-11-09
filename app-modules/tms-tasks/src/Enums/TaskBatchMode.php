<?php

namespace Dpb\Modules\Tasks\Enums;

enum TaskBatchMode: string
{
    case Uniform = 'uniform';
    case Diverse = 'diverse';

    public function label(): string
    {
        return match ($this) {
            self::Uniform => 'Rovnaké',
            self::Diverse => 'Rôzne',
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