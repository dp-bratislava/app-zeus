<?php

namespace App\Filament\Resources\TS\TicketItemResource\Pages;

use App\Filament\Resources\TS\TicketItemResource;
use App\Models\TicketAssignment;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketItem extends EditRecord
{
    protected static string $resource = TicketItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // $departmentId = app(TicketService::class)->getDepartment($this->record)?->id;
        // $vehicleId = app(TicketService::class)->getVehicle($this->record)?->id;

        // $data['department_id'] = $departmentId;
        // $data['vehicle_id'] = $vehicleId;

        $subjectId = TicketAssignment::whereBelongsTo($this->record->ticket)->first()?->subject?->id;

        $data['subject_id'] = $subjectId;
        // dd($activities);
        return $data;
    }
}
