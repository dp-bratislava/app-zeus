<?php

namespace App\Filament\Resources\DailyExpeditionResource\Forms;

use App\Filament\Components\ActivityTemplatePicker;
use App\Services\TS\ActivityService;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Forms\Components\Component;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;

class VehicleRepeater
{
    public static function make(string $uri, Collection|null $options = null): Component
    {
        return TableRepeater::make($uri)            
            ->grid(4)
            ->columnSpanFull()
            // ->columnSpan(4)
            ->headers([
                Header::make('vehicle')->label(__('daily-expedition.form.fields.vehicles.vehicle')),
                Header::make('state')->label(__('daily-expedition.form.fields.vehicles.state')),
                Header::make('service')->label(__('daily-expedition.form.fields.vehicles.service')),
                Header::make('note')->label(__('daily-expedition.form.fields.vehicles.note'))

            ])
            ->columns(4)
            ->schema([
                // vehicle
                Forms\Components\Hidden::make('vehicle_id'),
                Forms\Components\TextInput::make('vehicle_title')
                    ->columnSpan(1)
                    ->readOnly(),
                // Forms\Components\Select::make('vehicle')
                //     ->label(__('tickets/ticket-item.form.fields.title'))
                //     ->columnSpan(3)
                //     // ->options(fn() => Vehicle::pluck('code_1', 'id'))
                //     ->options(
                //         fn() => Vehicle::with('model')
                //         ->limit(10)
                //         ->get()
                //         ->map(function ($record) {
                //             return [
                //                 'id' => $record->id,
                //                 'label' => $record->code->code . ' - ' . $record?->model?->title
                //             ];
                //         })
                //     )
                //     ->searchable(),
                // state 
                Forms\Components\ToggleButtons::make('state')
                    ->options([
                        'ok' => 'Jazdí',
                        'split' => 'Delená',
                        'no' => 'Odstavené',
                    ])
                    ->colors([
                        'ok' => 'success',
                        'split' => 'warning',
                        'no' => 'danger',
                    ])
                    ->default('ok')
                    ->inline()
                    ->grouped()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('service')
                    ->columnSpan(1),
                Forms\Components\TextInput::make('note')
                    ->columnSpan(1),
            ])
            ->default($options)
            ->addable(false)
            ->deletable(false);
    }
}
