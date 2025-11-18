<?php

namespace App\Filament\Resources\Task\TaskResource\Pages;

use App\Filament\Resources\Task\TaskResource;
use Dpb\Package\TaskMS\Infrastructure\Persistence\Eloquent\Mappings\TaskMapper;
use Dpb\Package\Tasks\Services\CreateTaskService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function handleRecordCreation(array $data): Model    
    {       
        $task = app(CreateTaskService::class)->handle($data);
        return app(TaskMapper::class)->toEloquent($task);
    }    
}
