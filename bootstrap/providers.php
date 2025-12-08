<?php

return [
    App\Providers\AppServiceProvider::class,
        App\Providers\TaskMSFilamentPanelProvider::class,
    App\Providers\Filament\FleetPanelProvider::class,
    Dpb\Package\TaskMS\Providers\TaskMSFilamentPanelProvider::class,
];
