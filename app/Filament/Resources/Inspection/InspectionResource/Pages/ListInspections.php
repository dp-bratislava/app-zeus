<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Pages;

use App\Filament\Resources\Inspection\InspectionResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListInspections extends ListRecords
{
    protected static string $resource = InspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(fn () => dd('gg')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('all'),
            'daily-maintenance' => Tab::make('DO')
                ->modifyQueryUsing(fn(Builder $query) => $query->byTemplateGroup('daily-maintenance')),
            'stk' => Tab::make('STK')
                ->modifyQueryUsing(fn(Builder $query) => $query->byTemplateGroup('planned-maintenance')),
            'ek' => Tab::make('EK')
                ->modifyQueryUsing(fn(Builder $query) => $query->byTemplateGroup('ek')),
        ];
    }       
}
