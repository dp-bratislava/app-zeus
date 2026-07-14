# Upgrade guide

## Upgrading to 1.0.12

### Break activities

1. publish config
   ```bash
   cp /var/www/html/app-zeus/current/vendor/dpb/dpb-work-time-fund/config/dpb-wtf.conf /var/www/html/app-zeus/current/config/
   ```
2. set up rules for break activities in config (details in package docs `dpb/dpb-work-time-fund/docs`)
   1. Only departments 7213, 223, 7233
3. seed db with relevant data
   1. 
   ```bash
   # add break activities to db
   # build break activity - employee contract map in db
   # assign breaks to worktimes based on config rules
   php8.2 artisan data:migrate break-activity
   ```

### Task acces profiles

1. set relations for specific departments to see task groups and task item groups 
    ```bash
    # 
    php8.2 artisan data:migrate vehicle-cleaning-b
    ```

### Scalable operations

1. set operations in database as scalable
    ```bash
    # sets all oeprations belonging to depratment 9486 - cistenie vozidiel MHD as scalable 
    php8.2 artisan data:migrate scalable-operation
    ```