<?php

namespace App\Filament\Resources\TS\Task\TaskStatusResource\Pages;

use App\Filament\Resources\TS\Task\TaskStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTaskStatus extends CreateRecord
{
    protected static string $resource = TaskStatusResource::class;
}
