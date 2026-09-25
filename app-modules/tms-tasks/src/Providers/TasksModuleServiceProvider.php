<?php

namespace Dpb\Modules\Tasks\Providers;

use Dpb\Modules\Tasks\Filament\Plugins\TaskBatchPlugin;
use Dpb\Modules\Tasks\Models\AssetMovement;
use Dpb\Package\Assets\Models\AssetSlot;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\WorkTimeFund\Models\Task;
use Dpb\Modules\Tasks\Filament\Components\ActivityPicker\ActivityPickerComponent;
use Dpb\Modules\Tasks\Filament\Components\PhotoBufferManager;
use Dpb\Modules\Tasks\Filament\Components\PhotoGallery;
use Dpb\Modules\Tasks\Filament\Components\TaskItemPhotos;
use Dpb\Modules\Tasks\Filament\Components\WorkOrderInfoComponent;
use Dpb\Modules\Tasks\Filament\Components\WorkOrdersComponent;
use Dpb\Modules\Tasks\Filament\Plugins\WtfTmsBridgePlugin;
use Dpb\Modules\Tasks\Models\WorkOrder;
use Dpb\Modules\Tasks\Observers\TaskItemObserver;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Dpb\Modules\Tasks\Policies\TaskAssignmentPolicy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TasksModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            path: __DIR__.'/../../config/mod-tasks.php',
            key: 'dpb-mod-tasks'
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(
            paths: __DIR__.'/../../database/migrations'
        );

        $this->loadViewsFrom(
            path: __DIR__ . '/../../resources/views',
            namespace: 'dpb-mod-tasks'
        );

        $this->loadTranslationsFrom(
            path: __DIR__ . '/../../resources/lang',
            namespace: 'dpb-mod-tasks'
        );

        // $this->registerResourceTabs();


        $this->registerFilamentPlugin();
    }

    private function registerFilamentPlugin(): void
    {
        config()->set(key: 'admin-panel.plugins', value: array_merge(
            [TaskBatchPlugin::class],
            config(key: 'admin-panel.plugins', default: [])
        ));
    }
}

