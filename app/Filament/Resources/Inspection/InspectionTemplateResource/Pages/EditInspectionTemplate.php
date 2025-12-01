<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Pages;

use App\Filament\Resources\Inspection\InspectionTemplateResource;
use App\Models\InspectionTemplateAssignment;
use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditInspectionTemplate extends EditRecord
{
    protected static string $resource = InspectionTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('inspections/inspection-template.update_heading', ['title' => $this->record->title]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // dd($data);
        // // distance conditions
        // $data['cnd_distance_treshold'] = $this->record->getCondition('treshold', 'distance_traveled')?->value;
        // $data['cnd_distance_1adv'] = $this->record->getCondition('1-advance', 'distance_traveled')?->value;
        // $data['cnd_distance_2adv'] = $this->record->getCondition('2-advance', 'distance_traveled')?->value;

        // // time conditions
        // $data['cnd_time_treshold'] = $this->record->getCondition('treshold', 'days_in_service')?->value;
        // $data['cnd_time_1adv'] = $this->record->getCondition('1-advance', 'days_in_service')?->value;
        // $data['cnd_time_2adv'] = $this->record->getCondition('2-advance', 'days_in_service')?->value;

        // vehicle models
        $vehicleModelMorphClass = app(VehicleModel::class)->getMorphClass();
        $vehicleModels = InspectionTemplateAssignment::whereBelongsTo($this->record, 'template')
            ->whereMorphedTo('subject', $vehicleModelMorphClass)
            ->pluck('subject_id')
            ->toArray();
        $data['vehicle_models'] = $vehicleModels;

        return $data;
    }

    // protected function handleRecordUpdate(Model $record, array $data): Model
    // {
    //     $ticketItemRepo = app(TicketItemRepository::class);
    //     $result = $ticketItemRepo->update($record, $data);

    //     return $result;
    // }    
}
