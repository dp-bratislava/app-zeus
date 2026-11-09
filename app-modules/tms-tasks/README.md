# WIP - Readme

# Install

Module is part of aplication layer so it is installed only once. After that, all changes are made inplace no need to update composer package.

## Composer

### Enable module architecture

In `composer.json` add
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
artisan data:migrate batch-context
artisan data:migrate vehicle-cleaning
artisan data:migrate daily-maintenance
```

# More docs

More detailed docs are in `docs` directory