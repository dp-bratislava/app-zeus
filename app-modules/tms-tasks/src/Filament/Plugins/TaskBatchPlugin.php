<?php

namespace Dpb\Modules\Tasks\Filament\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Enums\Width;

class TaskBatchPlugin implements Plugin
{
    public function getId(): string
    {
        return 'dpb-mod-tasks';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->darkMode(false)
            ->maxContentWidth(Width::Full)
            ->resources(config('dpb-mod-tasks.filament_resources'));
    }

    public function boot(Panel $panel): void {}
}
