# WIP - Readme

# Install

Module is part of aplication layer so it is installed only once. After that, all changes are made inplace no need to update composer package.

## Composer

### Enable module architecture

In root `composer.json` add
```bash
    "repositories": [
        ...,
        {
            "type": "path",
            "url": "app-modules/*",
            "options": {
                "symlink": true
            }
        }         
    ],
```

### Run install 
```bash
composer require dpb/mod-tasks
```

## Run data migrations

**WARNING - NOT IDENPOTENT**

```bash
artisan data:migrate tb.batch-context
artisan data:migrate tb.vehicle-cleaning
artisan data:migrate tb.daily-maintenance
artisan data:migrate tb.reconcile-old-data
```

## Rebuild vehicle snapshot

```bash
sudo mysql app-zeus -e "
  TRUNCATE TABLE mvw_fleet_vehicle_snapshots;
  UPDATE report_sync_state SET last_synced_at = '1026-04-20 17:02:20' where report_name = 'mvw_fleet_vehicle_snapshots';
" \
&& artisan snapshot:sync fleet-vehicle
```

# More docs

More detailed docs are in `docs` directory