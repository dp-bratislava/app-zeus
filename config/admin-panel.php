<?php

use Dpb\Package\TaskMS\UI\Filament\Plugins\TaskMSPlugin;
use Dpb\WorkTimeFundFilament\Filament\Plugins\WtffPlugin;

return [
    'plugins' => [
        \Dpb\UserAdmin\UserAdminPlugin::class,
        // \Dpb\DatahubSync\Filament\Plugins\DatahubSyncPlugin::class,
        // WtffPlugin::class,
        TaskMSPlugin::class,
    ],
];
