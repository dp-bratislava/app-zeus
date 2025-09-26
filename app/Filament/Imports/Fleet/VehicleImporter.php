<?php

namespace App\Filament\Imports\Fleet;

use App\Models\Datahub\Department;
use App\Services\Fleet\VehicleService;
use Dpb\Package\Fleet\Models\LicencePlate;
use Dpb\Package\Fleet\Models\LicencePlateHistory;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleGroup;
use Dpb\Packages\Vehicles\Models\VehicleCode;
use Dpb\Packages\Vehicles\Models\VehicleCodeHistory;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VehicleImporter extends Importer
{
    protected const DEFAULT_DATE_FROM = '2000-01-01';

    protected static ?string $model = Vehicle::class;

    public function __construct(
        protected Import $import, 
        protected array $columnMap, 
        protected array $options, 
        protected VehicleService $vehicleService,
        protected Collection $departments,
        ) {
         $this->departments = Department::all()->pluck('id', 'code');
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code'),
            ImportColumn::make('vehicle_model')
                ->relationship('model', 'title')
            // ->rules(['required', 'max:255']),
                ->rules(['max:255']),
            // ImportColumn::make('end_of_warranty'),
            ImportColumn::make('licence_plate')
                // ->relationship('licencePlate', 'code')
            // ->rules(['required', 'max:255']),
                ->rules(['max:255']),
            ImportColumn::make('vehicle_group'),
            // department
            ImportColumn::make('department'),
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
        unset($this->data['department']);
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
        $licencePlate = $this->originalData['licence_plate'] == '' ? null : Str::trim($this->originalData['licence_plate']);         
        
        if (($licencePlate !== null) && (!in_array($licencePlate, ['N/A']))) {
            $licencePlateId = LicencePlate::createOrFirst([
                'code' => $licencePlate,                
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

        // vehicle codes
        $vehicleCode = $this->originalData['code'] == '' ? null : Str::trim($this->originalData['code']);         
        
        if (($vehicleCode !== null) && (!in_array($licencePlate, ['N/A']))) {
            $vehicleCodeId = VehicleCode::createOrFirst([
                'code' => $vehicleCode,                
            ])->id;

            $vehicleId = $this->record->id;

            $now = now();
            VehicleCodeHistory::create([
                'vehicle_id' => $vehicleId,
                'vehicle_code_id' => $vehicleCodeId,
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
       
        // vehicle department
        $rawDepartment = $this->originalData['department'] == '' ? null : Str::trim($this->originalData['department']);        
        if (($rawDepartment !== null) && (isset($this->departments[$rawDepartment]))) {            
            $this->vehicleService->setDepartment($this->record, $this->departments[$rawDepartment]);
        }        
    }    
}
