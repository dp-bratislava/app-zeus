<?php

namespace App\Filament\Resources\Ticket\TicketResource\Forms;

use App\Filament\Resources\Ticket\TicketResource\Components\ActivityRepeater;
use App\Filament\Resources\Ticket\TicketResource\Components\MaterialRepeater;
use App\Filament\Resources\Ticket\TicketResource\Components\ServiceRepeater;
use App\Services\Activity\Activity\WorkService;
use App\Services\Ticket\ActivityService;
use App\Services\Ticket\HeaderService;
use App\Services\Ticket\SubjectService;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketSource;
use Filament\Forms;
use Filament\Forms\Form;

class TicketForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label(__('tickets/ticket.form.fields.date'))
                    ->default(now()),
                Forms\Components\Select::make('group_id')
                    ->label(__('tickets/ticket.form.fields.title'))
                    ->relationship('group', 'title')
                    ->live(),
                Forms\Components\TextInput::make('title'),
                Forms\Components\ToggleButtons::make('source_id')
                    ->label(__('tickets/ticket.form.fields.source'))
                    // ->relationship('source', 'title')
                    ->inline()                    
                    ->options(fn() => TicketSource::pluck('title', 'id')),

                // Forms\Components\Select::make('source_id')
                //     ->relationship('source', 'title', null, true)
                //     ->preload()
                //     ->searchable()
                //     ->required(false),
                Forms\Components\TextInput::make('description')
                    ->label(__('tickets/ticket.form.fields.description')),
                // Forms\Components\Select::make('parent_id')
                //     ->relationship('parent', 'title', null, true)
                //     ->preload()
                //     ->searchable()
                //     ->required(false),
                //department
                // DepartmentPicker::make('department_id')
                //     ->relationship('department', 'title')
                //     ->getOptionLabelFromRecordUsing(null)
                //     ->getSearchResultsUsing(null)
                //     ->searchable()
                //     // ->default(function(TicketService $ticketService, $record) {
                //     //return $ticketService->getDepartment($record)->id;
                //     // return 283;
                //     // })
                //     // ->dehydrated(false)
                //     ->required(),
                // vehicle
                // Forms\Components\MorphToSelect::make('subject')
                //     ->types([
                //         MorphToSelect\Type::make(FleetVehicle::class)
                //             ->titleAttribute('code'),
                //     ])
                //     // ->relationship('subject', 'code')
                //     // ->options(fn() => Vehicle::pluck('code', 'id'))
                //     ->preload()
                //     ->searchable(),

                // states
                // Forms\Components\Select::make('state')                                        
                //     ->options(function($record) {
                //         $record->trtransitionableStateInstances();
                //     })
                //     ->preload()
                //     ->searchable(),                    

                // activities 
                Forms\Components\Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        // activities
                        Forms\Components\Tabs\Tab::make('activities')
                            ->label(__('tickets/ticket.form.tabs.activities'))
                            ->badge(3)
                            ->icon('heroicon-m-wrench')
                            ->schema([
                                ActivityRepeater::make('activities')
                                // ->relationship('activities'),
                            ]),
                        // materials
                        Forms\Components\Tabs\Tab::make('materials')
                            ->label(__('tickets/ticket.form.tabs.materials'))
                            ->icon('heroicon-m-rectangle-stack')
                            ->badge(2)
                            ->schema([
                                MaterialRepeater::make('materials')
                                // ->relationship('materials'),
                            ]),
                        // services
                        Forms\Components\Tabs\Tab::make('services')
                            ->label(__('tickets/ticket.form.tabs.services'))
                            ->badge(0)
                            ->icon('heroicon-m-user-group')
                            ->schema([
                                ServiceRepeater::make('services')
                                // ->relationship('services'),
                            ])
                    ]),
            ]);
    }
}
