<?php

namespace App\Providers;

use App\Snapshots\Core\SnapshotRegistry;
use App\Snapshots\Services\FleetVehicleSnapshot;
use App\Snapshots\Services\HRContractSnapshot;
use App\Snapshots\Services\TaskItemSnapshot;
use App\Snapshots\Services\WorkActivityReport;
use App\Snapshots\Services\WorkTaskSubjectSnapshot;
use Illuminate\Support\ServiceProvider;

class SnapshotServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SnapshotRegistry::class, function () {
            $registry = new SnapshotRegistry();
            // snapshots
            $registry->register('hr-contract', HRContractSnapshot::class);
            $registry->register('tms-task-item', TaskItemSnapshot::class);
            $registry->register('work-task-subject', WorkTaskSubjectSnapshot::class);
            $registry->register('fleet-vehicle', FleetVehicleSnapshot::class);
            // reports
            $registry->register('work-activity', WorkActivityReport::class);

            return $registry;
        });
    }
}
