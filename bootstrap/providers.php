<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\FleetPanelProvider;
use App\Providers\SnapshotServiceProvider;
use App\Providers\TelescopeServiceProvider;

return [
    AppServiceProvider::class,
    FleetPanelProvider::class,
    SnapshotServiceProvider::class,
    TelescopeServiceProvider::class,
];
