<?php

namespace App\Filament\Resources\TS\Task\TaskResource\Pages;

use App\Filament\Resources\TS\Task\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
}
