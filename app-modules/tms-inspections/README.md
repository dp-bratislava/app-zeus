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
composer require dpb/mod-inspections
```