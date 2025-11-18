<?php

namespace App\Filament\Resources\Incident\IncidentResource\Pages;

use App\Filament\Resources\Incident\IncidentResource;
use Dpb\Package\Incidents\Models\IncidentType;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    } 

    public function getTabs(): array
    {
        $tabs = [];

        // Default “all” tab
        $tabs['all'] = Tab::make('Všetky');

        // Dynamic tabs
        foreach (IncidentType::get() as $type) {
            $tabs[$type->code] = Tab::make($type->title)
                ->modifyQueryUsing(
                    function (Builder $query) use ($type) {
                        $query->whereHas('incident', function ($q) use ($type) {
                            $q->byType($type->code);
                        });
                    }
                );
        }

        return $tabs;
    }
}
