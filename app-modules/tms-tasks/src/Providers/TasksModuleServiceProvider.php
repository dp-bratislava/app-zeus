<?php

namespace Dpb\Modules\Tasks\Providers;

use Dpb\Modules\Tasks\Eligibility\SubjectEvaluatorRegistry;
use Dpb\Modules\Tasks\Eligibility\VehicleRuleEvaluator;
use Dpb\Modules\Tasks\Filament\Plugins\TaskBatchPlugin;
use Dpb\Modules\Tasks\TaskBatches\Livewire\AssignmentMatrix;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TasksModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SubjectEvaluatorRegistry::class, function () {
            $registry = new SubjectEvaluatorRegistry();

            $registry->register('vehicle', VehicleRuleEvaluator::class);

            return $registry;
        });


        $this->mergeConfigFrom(
            path: __DIR__ . '/../../config/mod-tasks.php',
            key: 'dpb-mod-tasks'
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(
            paths: __DIR__ . '/../../database/migrations'
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

        Livewire::component(
            'tms-tasks.assignment-matrix',
            AssignmentMatrix::class,
        );
    }

    private function registerFilamentPlugin(): void
    {
        config()->set(key: 'admin-panel.plugins', value: array_merge(
            [TaskBatchPlugin::class],
            config(key: 'admin-panel.plugins', default: [])
        ));

        FilamentAsset::register([
            Css::make(
                'dpb-mod-tasks',
                __DIR__ . '/../resources/dist/filament.css',
            ),
        ], package: 'dpb/mod-tasks');
    }
}
