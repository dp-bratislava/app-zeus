<?php

use Dpb\WorkTimeFundFilament\Filament\Plugins\WtffPlugin;

return [
    'plugins' => [
        \Dpb\UserAdmin\UserAdminPlugin::class,
        // \Dpb\DatahubSync\Filament\Plugins\DatahubSyncPlugin::class,
        WtffPlugin::class,
    ],
];
