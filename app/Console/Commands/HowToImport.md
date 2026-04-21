Kontrola, kolko zamestnancov nema smeny pritom ma pracu
```
php8.2 artisan app:check-existing-work-times
```
Update smien z datahubu
```
php8.2 artisan app:sync-worktimes-asphere-import
```
## kontroly, poruchy (vsetky strediska - "combined_" tabulky)
Vytvorenie operácií pre tabulky combined_poruchy a combined_kontroly
```
php8.2 artisan app:create-missing-asphere-operations
```
```
php8.2 artisan app:import-asphere-kontroly
```
```
php8.2 artisan app:import-asphere-poruchy
```
## Zazemne prace elektricky, trolejbusy (tabulka "zazemne_elektrika")
```
php8.2 artisan app:create-missing-asphere-operations --tables=zazemne_elektrika --operation-title-column=operation --duration-column=duration --category-title="zazemne prace z aspheru"
```
```
php8.2 artisan app:import-zazemne-prace
```
## Denne osetrenia elektricky, trolejbusy (tabulka "DO_elektrika")
```
php8.2 artisan app:create-missing-asphere-operations --tables=DO_elektrika --operation-title-column=operation --duration-column=duration --category-title="denne osetrenia z asphere" --round-to-minutes
```
```
php8.2 artisan import:daily-inspection
```