<?php

namespace Dpb\Modules\Inspections\Providers;

use Dpb\Modules\Inspections\Filament\Plugins\InspectionPlugin;
use Dpb\Modules\Inspections\InspectionBatches\Policies\InspectionBatchPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class InspectionsModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            path: __DIR__ . '/../../config/mod-inspections.php',
            key: 'dpb-mod-inspections'
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(
            paths: __DIR__ . '/../../database/migrations'
        );

        $this->loadViewsFrom(
            path: __DIR__ . '/../../resources/views',
            namespace: 'dpb-mod-inspections'
        );

        $this->loadTranslationsFrom(
            path: __DIR__ . '/../../resources/lang',
            namespace: 'dpb-mod-inspections'
        );

        // Gate::policy(InspectionBatch::class, InspectionBatchPolicy::class);

        $this->registerFilamentPlugin();

        // Livewire::component(
        //     'tms-inspections.assignment-matrix',
        //     AssignmentMatrix::class,
        // );
    }

    private function registerFilamentPlugin(): void
    {
        config()->set(key: 'admin-panel.plugins', value: array_merge(
            [InspectionPlugin::class],
            config(key: 'admin-panel.plugins', default: [])
        ));

    }
}
