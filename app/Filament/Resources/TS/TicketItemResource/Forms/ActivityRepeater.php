<?php

namespace App\Filament\Resources\TS\TicketItemResource\Forms;

use App\Filament\Components\ActivityTemplatePicker;
use App\Models\ActivityTemplatable;
use App\Services\TS\ActivityService;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Filament\Forms\Components\Component;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Forms\Set;

class ActivityRepeater
{
    public static function make1(string $uri): Component
    {
        return TableRepeater::make($uri)
            ->defaultItems(0)
            ->cloneable()
            ->columnSpanFull()
            ->headers([
                Header::make('date')->label(__('tickets/ticket-item.form.fields.activities.date')),
                Header::make('template')->label(__('tickets/ticket-item.form.fields.activities.template')),
                Header::make('work_log')->label(__('tickets/ticket-item.form.fields.activities.work_log.title')),
                // Header::make('e1'),
                // Header::make('e2'),
            ])
            ->schema([
                //date
                Forms\Components\DatePicker::make('date')
                    ->columnSpan(1)
                    ->default(now()),
                // activity template
                Forms\Components\Select::make('activity_template_id')
                    // ->relationship('template', 'title')
                    ->label(__('tickets/ticket-item.form.fields.title'))
                    ->columnSpan(3)
                    // ->options(fn(Get $get) => ActivityTemplate::where('template_group_id', $get('../activity_template_group_id'))
                    //     ->pluck('title', 'id')
                    // )
                    ->options(fn() => ActivityTemplate::pluck('title', 'id'))
                    ->searchable(),
                // Forms\Components\Select::make('activity_template_id')
                //     ->label(__('tickets/ticket-item.form.fields.title'))
                //     ->columnSpan(5)
                //     // ->options(fn(Get $get) => ActivityTemplate::where('template_group_id', $get('../activity_template_group_id'))
                //     //     ->pluck('title', 'id')
                //     // )
                //     ->searchable(),                      
                // ActivityTemplatePicker::make('activity_template_id')
                //     // ->relationship('template', 'title')
                //     ->getOptionLabelFromRecordUsing(null)
                //     ->getSearchResultsUsing(null)
                //     ->live()
                //     ->afterStateUpdated(function (Set $set, Get $get) {
                //         $template = ActivityTemplate::find($get('activity_template_id'));
                //         $set('template_duration', $template?->duration);
                //     })
                //     ->searchable(),
                // Forms\Components\TextInput::make('template_duration')                    
                //     ->readOnly()
                //     ->dehydrated(),
                WorkAssignmentRepeater::make('workAssignments')
                    ->label(__('tickets/ticket-item.form.fields.activities.work_log.title'))
                    // ->relationship('workAssignments')
                    ->columnSpan(5)
            ])
            // ->(function($record, ActivityService $svc) {
            //     return $svc->getActivities($record);  
            // })
            ->default([]);
    }

    public static function make(string $uri): Component
    {
        return Repeater::make($uri)
            ->defaultItems(0)
            ->cloneable()
            ->reorderable(false)
            ->columns(5)
            ->columnSpanFull()
            ->schema([
                //date
                Forms\Components\DatePicker::make('date')
                    ->label(__('tickets/ticket-item.form.fields.date'))
                    ->columnSpan(1)
                    ->default(now()),
                // activity template
                Forms\Components\Select::make('activity_template_id')
                    // ->relationship('template', 'title')
                    ->label(__('tickets/ticket-item.form.fields.title'))
                    ->columnSpan(4)
                    ->options(
                        function (Get $get) {
                            // ActivityTemplate::where('template_group_id', $get('../activity_template_group_id'))
                            //     ->pluck('title', 'id')
                            $vehicleModelId = '128';
                            $ticketItemGroupId = '1';
                            return ActivityTemplatable::byTemplatable('vehicle-model', $vehicleModelId)
                                ->byTemplatable('ticket-item-group', $ticketItemGroupId)
                                ->get()
                                ->mapWithKeys(fn($templatable) => [
                                    $templatable->template->id => "{$templatable->template->title} {$templatable->template->duration}" 
                                ]);
                        }
                    )
                    // ->options(fn() => ActivityTemplate::pluck('title', 'id'))
                    ->searchable(),
                // Forms\Components\Select::make('activity_template_id')
                //     ->label(__('tickets/ticket-item.form.fields.title'))
                //     ->columnSpan(5)
                //     // ->options(fn(Get $get) => ActivityTemplate::where('template_group_id', $get('../activity_template_group_id'))
                //     //     ->pluck('title', 'id')
                //     // )
                //     ->searchable(),                      
                // ActivityTemplatePicker::make('activity_template_id')
                //     // ->relationship('template', 'title')
                //     ->getOptionLabelFromRecordUsing(null)
                //     ->getSearchResultsUsing(null)
                //     ->live()
                //     ->afterStateUpdated(function (Set $set, Get $get) {
                //         $template = ActivityTemplate::find($get('activity_template_id'));
                //         $set('template_duration', $template?->duration);
                //     })
                //     ->searchable(),
                // Forms\Components\TextInput::make('template_duration')                    
                //     ->readOnly()
                //     ->dehydrated(),
                WorkAssignmentRepeater::make('workAssignments')
                    ->label(__('tickets/ticket-item.form.fields.activities.work_log.title'))
                    // ->relationship('workAssignments')
                    ->columnSpan(5)
            ])
            // ->(function($record, ActivityService $svc) {
            //     return $svc->getActivities($record);  
            // })
            ->default([]);
    }
}
