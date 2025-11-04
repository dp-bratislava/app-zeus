<?php

namespace App\Filament\Resources\Incident\IncidentResource\Pages;

use App\Filament\Resources\Incident\IncidentResource;
use App\Models\IncidentAssignment;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Všetky'),
            'accidents' => Tab::make('Nehody')
                ->modifyQueryUsing(fn(Builder $query) => $query->byType('accident')),
            'malfuctions' => Tab::make('Poruchy')
                ->modifyQueryUsing(fn(Builder $query) => $query->byType('malfunction')),
        ];
    }        
}
