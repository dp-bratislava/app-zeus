<?php

namespace App\Filament\Imports\Fleet;

use Dpb\Packages\Vehicles\Models\LicencePlate;
use Dpb\Packages\Vehicles\Models\LicencePlateHistory;
use Dpb\Packages\Vehicles\Models\Vehicle;
use Dpb\Packages\Vehicles\Models\VehicleGroup;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class VehicleImporter extends Importer
{
    protected const DEFAULT_DATE_FROM = '2000-01-01';

    protected static ?string $model = Vehicle::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code'),
            ImportColumn::make('vehicle_model')
                ->relationship('model', 'title')
            // ->rules(['required', 'max:255']),
                ->rules(['max:255']),
            // ImportColumn::make('end_of_warranty'),
            ImportColumn::make('licence_plate'),
            ImportColumn::make('vehicle_group'),
        ];
    }

    public function resolveRecord(): ?Vehicle
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Vehicle();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your vehicle import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function beforeFill(): void
    {
        // unset fields from import, that should not be mapped 
        // into dispense model
        unset($this->data['licence_plate']);
        unset($this->data['vehicle_group']);
        unset($this->data['']);
    }

    /**
     * Summary of afterSave
     * 
     * Extend afterSave hook
     * Create material items for imported dispense
     * 
     * @return void
     */
    protected function afterSave(): void
    {
        // licence plates
        $rawVehicleGroup = $this->originalData['licence_plate'] == '' ? null : Str::trim($this->originalData['licence_plate']);        
        if ($rawVehicleGroup !== null) {
            $licencePlateId = LicencePlate::createOrFirst([
                'code' => $rawVehicleGroup,                
            ])->id;

            $vehicleId = $this->record->id;

            $now = now();
            LicencePlateHistory::create([
                'vehicle_id' => $vehicleId,
                'licence_plate_id' => $licencePlateId,
                'date_from' => self::DEFAULT_DATE_FROM,
                'created_at' => $now,
                'updated_at' => $now,
            ]); 
        }

        // vehicle groups
        $rawVehicleGroup = $this->originalData['vehicle_group'] == '' ? null : Str::trim($this->originalData['vehicle_group']);        
        if ($rawVehicleGroup !== null) {
            $vehicleGroupId = VehicleGroup::createOrFirst([
                'code' => $rawVehicleGroup,                
                'title' => $rawVehicleGroup,                
            ])->id;

            $this->record->groups()->attach($vehicleGroupId);
        }
        
    }    
}
