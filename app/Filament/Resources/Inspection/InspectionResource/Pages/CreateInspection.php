<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Pages;

use App\Filament\Resources\Inspection\InspectionResource;
use App\Models\InspectionAssignment;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInspection extends CreateRecord
{
    protected static string $resource = InspectionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);
        $data['subject_type'] = 'vehicle';
        return app(InspectionAssignment::class)->create($data);
    }    
}
