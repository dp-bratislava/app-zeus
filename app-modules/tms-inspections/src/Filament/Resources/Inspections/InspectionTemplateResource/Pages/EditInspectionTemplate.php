<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Pages;

use Dpb\Package\Fleet\Models\VehicleModel;
use Dpb\Package\TaskMS\Models\InspectionTemplatable;
use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource;
use Dpb\Modules\Inspections\Mappers\Inspection\InspectionTemplateUpdateFormMapper;
use Dpb\Package\TaskMS\Workflows\UpdateInspectionTemplateWorkflow;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EditInspectionTemplate extends EditRecord
{
    protected static string $resource = InspectionTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('dpb-mod-inspections::inspection-template.update_heading', ['title' => $this->record->title]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // templatables / task item groups
        $tigMorphClass = app(TaskItemGroup::class)->getMorphClass();
        $tigs = InspectionTemplatable::whereBelongsTo($this->record, 'template')
            ->whereMorphedTo('templatable', $tigMorphClass)
            ->pluck('templatable_id')
            ->toArray();
        $data['templatables'][$tigMorphClass] = $tigs;

        // templatables / vehicle models
        $vehicleModelMorphClass = app(VehicleModel::class)->getMorphClass();
        $vehicleModels = InspectionTemplatable::whereBelongsTo($this->record, 'template')
            ->whereMorphedTo('templatable', $vehicleModelMorphClass)
            ->pluck('templatable_id')
            ->toArray();
        $data['templatables'][$vehicleModelMorphClass] = $vehicleModels;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $command = app(InspectionTemplateUpdateFormMapper::class)->fromForm($record, $data);

        return app(UpdateInspectionTemplateWorkflow::class)->handle($command);
    }
}
