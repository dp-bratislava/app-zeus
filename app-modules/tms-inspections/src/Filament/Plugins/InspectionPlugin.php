<?php

namespace Dpb\Modules\Inspections\Filament\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Enums\Width;

class InspectionPlugin implements Plugin
{
    public function getId(): string
    {
        return 'dpb-mod-inspections';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->darkMode(false)
            ->maxContentWidth(Width::Full)
            ->resources(config('dpb-mod-inspections.filament_resources'));
    }

    public function boot(Panel $panel): void {}
}
