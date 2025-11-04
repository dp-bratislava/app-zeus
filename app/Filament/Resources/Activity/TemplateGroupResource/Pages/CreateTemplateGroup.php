<?php

namespace App\Filament\Resources\Activity\TemplateGroupResource\Pages;

use App\Filament\Resources\Activity\TemplateGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateTemplateGroup extends CreateRecord
{
    protected static string $resource = TemplateGroupResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('activities/activity-template-group.form.create_heading');
    }    
}
