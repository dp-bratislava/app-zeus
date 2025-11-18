<?php

namespace App\Filament\Resources\Task\TaskGroupResource\Pages;

use App\Filament\Resources\Task\TaskGroupResource;
use Dpb\Package\Tasks\Services\UpdateTaskGroupService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTaskGroup extends EditRecord
{
    protected static string $resource = TaskGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model    
    {       
        $taskGroup = app(UpdateTaskGroupService::class)->handle($record->id, $data);
        return $record;
    }       
}
