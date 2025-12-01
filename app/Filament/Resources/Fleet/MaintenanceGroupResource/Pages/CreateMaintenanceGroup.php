<?php

namespace App\Filament\Resources\Fleet\MaintenanceGroupResource\Pages;

use App\Filament\Resources\Fleet\MaintenanceGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateMaintenanceGroup extends CreateRecord
{
    protected static string $resource = MaintenanceGroupResource::class;
    
    public function getTitle(): string | Htmlable
    {
        return __('fleet/maintenance-group.create_heading');
    }  
}
