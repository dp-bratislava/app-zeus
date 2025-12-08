<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Pages;

use App\Filament\Resources\Inspection\InspectionResource;
use App\Models\InspectionAssignment;
use App\UseCases\InspectionAssignment\CreateInspectionAssignmentUseCase;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateInspection extends CreateRecord
{
    protected static string $resource = InspectionResource::class;
    
    public function getTitle(): string | Htmlable
    {
        return __('inspections/inspection.create_heading');
    }

    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);
        return app(CreateInspectionAssignmentUseCase::class)->execute($data);
    }    

    // protected function handleRecordCreation(array $data): Model
    // {
    //     // dd($data);
    //     $data['subject_type'] = 'vehicle';
    //     return app(InspectionAssignment::class)->create($data);
    // }    
}
