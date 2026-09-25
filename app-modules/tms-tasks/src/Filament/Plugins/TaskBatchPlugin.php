<?php

namespace Dpb\Modules\Tasks\Filament\Plugins;

use Dpb\Modules\Tasks\Filament\Pages\DailyMaintenanceWorkOrdersPage;
use Dpb\Modules\Tasks\Filament\Pages\TaskBatchWorkOrdersPage;
use Dpb\Modules\Tasks\Filament\Pages\WorkOrderPage;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;

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
            ->pages(pages: [
                TaskBatchWorkOrdersPage::class,
            ])
            // ->renderHook(
            //     PanelsRenderHook::USER_MENU_BEFORE,
            //     fn() => view('dpb-wtf-tms-bridge::filament.components.top-menu-help-button')->render()
            // )
            ->maxContentWidth(Width::Full)
            ->resources(config('dpb-mod-tasks.filament_resources'));
    }

    public function boot(Panel $panel): void {}
}
