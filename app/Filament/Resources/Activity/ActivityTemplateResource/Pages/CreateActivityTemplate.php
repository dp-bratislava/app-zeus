<?php

namespace App\Filament\Resources\Activity\ActivityTemplateResource\Pages;

use App\Filament\Resources\Activity\ActivityTemplateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateActivityTemplate extends CreateRecord
{
    protected static string $resource = ActivityTemplateResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('activities/activity-template.form.create_heading');
    }     

  
}
