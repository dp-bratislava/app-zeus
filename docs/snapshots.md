# WIP Snapshots

Denormalised tables for faster reads. 

* Snapshot tables are for further use
* Report tables are tailored for specific reports

Currently it contains these tables

### Snapshot tables

| Key | Table | Description |
|--|--|--|
| tms-task-item | mvw_task_item_snapshots | TMS task item metadata. date, task item group, task group, ... |
| fleet-vehicle | mvw_fleet_vehicle_snapshots | Fleet vehicle metadata. model, code, licence plate |
| hr-contract | mvw_hr_contract_snapshots | HR datahub employee contract metadata. PID, name, profession, ... |
| work-task-subject | mvw_work_task_subject_snapshots | Resolved WTF task subjects. Vehicles, tables or custom attribute option/type subjects |

### Report tables

| Key | Table | Description |
|--|--|--|
| work-activity | mvw_work_activity_report | Detailed report per each activity report |

## Adding new snapshot

### 1. Add snapshot class

* Contains snasphot query and logic
* Default placement App/Snapshots/Srvices/
* Extends `App\Snapshots\Core\Contracts\SnapshotContract`

### 2. Register snapshot class

* In `App\Providers\` add entry mapping snapshot key to snapshot service class
```php
<?php

namespace App\Providers;

...

class SnapshotServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SnapshotRegistry::class, function () {
            $registry = new SnapshotRegistry();

            ...

            $registry->register('work-task-subject', 
            WorkTaskSubjectSnapshot::class);

            ...

            return $registry;
        });
    }
}
```

@TO DO
### 4. Optional add snapshot eloquent model
read only

### 5. Set up sync state entry

Add entry to `report_sync_state` database table

example
```sql
INSERT INTO `report_sync_state` (`report_name`, `last_synced_at`, `created_at`, `updated_at`) VALUES ('work-task-subject', '1026-05-06 10:50:54', NOW(), NOW());
```

@TO DO
**last_synced_at** should be some old date so that all records are being synced at first run. Will be changed by command params in future.

## Running snapshot sync

**laravel query has to be running for this to work**

### Command line

Run snapshot sync via command line. In default state it syncs only changes since last sync.

```bash
# php8.2 artisan sync:snapshot <snapshot_name>
php8.2 artisan sync:snapshot work-task-subject
```

@TO DO
### Scheduler

