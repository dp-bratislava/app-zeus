<?php

namespace App\Filament\Resources\Fleet\MaintenanceGroupResource\Pages;

use App\Filament\Resources\Fleet\MaintenanceGroupResource;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EditMaintenanceGroup extends EditRecord
{
    protected static string $resource = MaintenanceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('fleet/maintenance-group.form.update_heading', ['title' => $this->record->code]);
    }  
    
    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     // vehicles
    //     $data['vehicles'] = $this->record->with(['vehicles'])->vehicles->pluck('id')->toArray();
    //     dd($this->record->vehicles);

    //     return $data;
    // }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        Vehicle::whereIn('id', $data['vehicles'])->update(['maintenance_group_id' => $record->id]);

        $record->update($data);
        return $record;
    }     
}
