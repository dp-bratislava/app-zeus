<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Pages;

use App\Filament\Resources\Inspection\InspectionTemplateResource;
use App\Services\InspectionTemplateRepository;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInspectionTemplate extends CreateRecord
{
    protected static string $resource = InspectionTemplateResource::class;

    public function handleRecordCreation(array $data): Model
    {
        // dd('gg');
        $inspectionTemplate = app(InspectionTemplateRepository::class)->create($data);
        return $inspectionTemplate;   
    }
}
