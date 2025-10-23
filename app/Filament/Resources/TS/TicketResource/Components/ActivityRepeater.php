<?php

namespace App\Filament\Resources\TS\TicketResource\Components;

use App\Filament\Components\ActivityTemplatePicker;
use App\Services\TS\ActivityService;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Filament\Forms\Components\Component;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;

class ActivityRepeater
{
    public static function make(string $uri): Component
    {
        return TableRepeater::make($uri)
            ->defaultItems(0)
            ->cloneable()
            ->columnSpan(5)
            ->headers([
                Header::make('date'),
                Header::make('template'),
                // Header::make('pracovne vykony'),
                // Header::make('e1'),
                // Header::make('e2'),
            ])
            ->schema([                
                Forms\Components\DatePicker::make('date')
                    ->default(now()),
                ActivityTemplatePicker::make('activity_template_id')
                    // ->relationship('template', 'title')
                    ->getOptionLabelFromRecordUsing(null)
                    ->getSearchResultsUsing(null)
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $template = ActivityTemplate::find($get('activity_template_id'));
                        $set('template_duration', $template?->duration);
                    })
                    ->searchable(),
                // Forms\Components\TextInput::make('template_duration')                    
                //     ->readOnly()
                //     ->dehydrated(),
                // WorkAssignmentRepeater::make('workAssignments')
                //     // ->relationship('workAssignments')
                //     ->columnSpan(5)
            ])
            // ->(function($record, ActivityService $svc) {
            //     return $svc->getActivities($record);  
            // })
            ->default([]);
    }
}
