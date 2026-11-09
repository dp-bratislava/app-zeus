<?php

namespace Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Pages;

use Dpb\Departments\Livewire\DepartmentSwitcherComponent;
use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignmentResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListInspectionAssignments extends ListRecords
{
    protected static string $resource = InspectionAssignmentResource::class;

    protected $listeners = [
        DepartmentSwitcherComponent::EVENT_DEPARTMENT_CHANGED => '$refresh',
    ];

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            // ->using(fn () => dd('gg')),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    } 
}
