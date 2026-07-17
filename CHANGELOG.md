# Changelog

## 2026-06-12 - 1.0.12
### Added
- Added individual day worktime shif sync 
- Added default fixed operations automaticaly added to worktime based on some criteria (e.g. 15 min for shower)
- Added scalable operations mechanics 
- Added multiple redirect after save options for work assignment to TaskItem
### Changed
- Changed filtering of displayed categories when adding work to taskItems to also use activeDepartment
- Changed TaskAssignment and DailyMaintenance access mechanics to use policy and permission profiles
### Removed
- unused filament importer classes from application layer