<?php

use Dpb\WorkTimeFundFilament\Filament\Plugins\WtffPlugin;

return [
    'plugins' => [
        \Dpb\UserAdmin\UserAdminPlugin::class,
        \Dpb\DatahubSync\DatahubSyncPlugin::class,
        WtffPlugin::class,
    ],
];
