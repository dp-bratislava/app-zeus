<?php

namespace App\Filament\Resources\Task\TaskGroupResource\Pages;

use App\Filament\Resources\Task\TaskGroupResource;
use Dpb\Package\TaskMS\Infrastructure\Persistence\Eloquent\Mappings\TaskGroupMapper;
use Dpb\Package\Tasks\Entities\TaskGroup;
use Dpb\Package\Tasks\Services\CreateTaskGroupService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTaskGroup extends CreateRecord
{
    protected static string $resource = TaskGroupResource::class;

    protected function handleRecordCreation(array $data): Model    
    {       
        $taskGroup = app(CreateTaskGroupService::class)->handle($data);
        return app(TaskGroupMapper::class)->toEloquent($taskGroup);
    }    
}
