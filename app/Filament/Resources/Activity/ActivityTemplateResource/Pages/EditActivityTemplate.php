<?php

namespace App\Filament\Resources\Activity\ActivityTemplateResource\Pages;

use App\Filament\Resources\Activity\ActivityTemplateResource;
use App\Models\ActivityTemplateAssignment;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditActivityTemplate extends EditRecord
{
    protected static string $resource = ActivityTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('activities/activity-template.form.update_heading');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $subjectId = ActivityTemplateAssignment::whereBelongsTo($this->record, 'template')->first()?->subject?->id;

        $data['subject_id'] = $subjectId;
        return $data;
    }
}
