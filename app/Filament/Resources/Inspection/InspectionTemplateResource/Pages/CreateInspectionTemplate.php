<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Pages;

use App\Filament\Resources\Inspection\InspectionTemplateResource;
use App\Services\InspectionTemplateRepository;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateInspectionTemplate extends CreateRecord
{
    protected static string $resource = InspectionTemplateResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('inspections/inspection-template.create_heading');
    }  

    // public function handleRecordCreation(array $data): Model
    // {
    //     // dd('gg');
    //     $inspectionTemplate = app(InspectionTemplateRepository::class)->create($data);
    //     return $inspectionTemplate;   
    // }
}
