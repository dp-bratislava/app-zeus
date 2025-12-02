<?php

namespace App\Filament\Resources\TS\TicketItemResource\Forms;

use App\Filament\Resources\TS\TicketItemResource\Forms\ActivityRepeater;
use App\Filament\Resources\TS\TicketResource\Components\MaterialRepeater;
use App\Filament\Resources\TS\TicketResource\Components\ServiceRepeater;
use App\States\TS\TicketItem\Closed;
use App\States\TS\TicketItem\Created;
use App\States\TS\TicketItem\InProgress;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketItemGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;

class TicketItemAssignmentForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->columns(7)
            ->schema([
                // ticket
                Forms\Components\Select::make('ticket')
                    ->label(__('tickets/ticket-item.form.fields.ticket'))
                    ->columnSpan(3)
                    ->relationship('ticket', 'title'),
                    // ->getOptionLabelFromRecordUsing(fn (Ticket $record) => "{$record->}"),
                    
                // date
                Forms\Components\DatePicker::make('date')
                    ->label(__('tickets/ticket-item.form.fields.date'))
                    ->columnSpan(1)
                    ->default(now()),
                // // subject
                // Forms\Components\Select::make('subject_id')
                //     ->label(__('tickets/ticket-item.form.fields.subject'))
                //     ->columnSpan(1)
                //     // ->relationship('source', 'title', null, true)
                //     ->options(fn() => Vehicle::pluck('code_1', 'id'))
                //     ->getOptionLabelsUsing(fn($record) => "{$record->code->code} - {$record->model->title}")
                //     ->preload()
                //     ->searchable()
                //     // ->disabled(fn($record) => $record->source_id == TicketSource::byCode('planned-maintenance')->first()->id)
                //     ->required(false)
                //     ->hiddenOn(TicketItemRelationManager::class),

                // title
                Forms\Components\Select::make('ticketItem.group_id')
                    ->relationship('ticketItem.group', 'title')
                    ->label(__('tickets/ticket-item.form.fields.title'))
                    ->columnSpan(2)
                    // ->options(fn() => ActivityTemplateGroup::has('parent')->pluck('title', 'id'))
                    ->getOptionLabelFromRecordUsing(fn(TicketItemGroup $record) => "{$record->code} {$record->title}")
                    ->searchable()
                    ->preload()
                    ->live(),

                // assigned to e.g. maintenance group
                Forms\Components\ToggleButtons::make('assigned_to_id')
                    ->label(__('tickets/ticket-item.form.fields.assigned_to'))
                    ->columnSpan(2)
                    ->options(fn() => MaintenanceGroup::pluck('code', 'id'))
                    // ->default(function (RelationManager $livewire) {

                    //     return $livewire->getOwnerRecord()->assignedTo?->id;
                    // })
                    ->inline(),

                // // state
                // Forms\Components\ToggleButtons::make('state')
                //     ->label(__('tickets/ticket-item.form.fields.state'))
                //     ->columnSpan(2)
                //     ->options(fn() => [
                //         Created::$name => __('tickets/ticket-item.states.created'),
                //         InProgress::$name => __('tickets/ticket-item.states.in-progress'),
                //         Closed::$name => __('tickets/ticket-item.states.closed'),
                //     ])
                //     ->inline(),

                // // Forms\Components\TextInput::make('title')
                // //     ->columnSpan(3)
                // //     ->label(__('tickets/ticket-item.form.fields.title')),
                // Forms\Components\Textarea::make('description')
                //     ->label(__('tickets/ticket-item.form.fields.description'))
                //     ->columnSpanFull(),

                // supervised by

                // // activities 
                // Forms\Components\Tabs::make('all_tabs')
                //     ->columnSpan(4)
                //     ->tabs([
                //         // activities
                //         Forms\Components\Tabs\Tab::make('activities')
                //             ->label(__('tickets/ticket-item.form.tabs.activities'))
                //             ->badge(fn ($record) => $record->activities?->count() ?? 0)
                //             ->icon('heroicon-m-wrench')
                //             ->schema([
                //                 ActivityRepeater::make('activities')
                //                     ->label(__('tickets/ticket-item.form.fields.activities.title'))
                //                 // ->relationship('activities'),
                //             ]),
                //         // materials
                //         Forms\Components\Tabs\Tab::make('materials')
                //             ->label(__('tickets/ticket-item.form.tabs.materials'))
                //             ->icon('heroicon-m-rectangle-stack')
                //             ->badge(fn ($record) => $record->materials?->count() ?? 0)
                //             ->schema([
                //                 MaterialRepeater::make('materials')
                //                 // ->relationship('materials'),
                //             ]),
                //         // services
                //         Forms\Components\Tabs\Tab::make('services')
                //             ->label(__('tickets/ticket-item.form.tabs.services'))
                //             ->badge(0)
                //             ->icon('heroicon-m-user-group')
                //             ->schema([
                //                 ServiceRepeater::make('services')
                //                 // ->relationship('services'),
                //             ])
                //     ]),

                // history / comments
                // Forms\Components\Tabs::make('comments_tabs')
                //     ->columnSpan(3)
                //     ->tabs([
                //         // comments
                //         Forms\Components\Tabs\Tab::make('comments_tab')
                //             ->label(__('tickets/ticket-item.form.tabs.comments'))
                //             ->badge(3)
                //             ->icon('heroicon-m-wrench')
                //             ->schema([
                //                 TableRepeater::make('comments')
                //                     ->headers([
                //                         Header::make('created_at')->label(__('tickets/ticket-item.form.fields.activities.date')),
                //                         Header::make('author')->label(__('tickets/ticket-item.form.fields.activities.template')),
                //                         Header::make('body')->label(__('tickets/ticket-item.form.fields.activities.template')),
                //                     ])
                //                     ->schema([
                //                         Forms\Components\DateTimePicker::make('date1'),
                //                         Forms\Components\RichEditor::make('body')
                //                     ])
                //                     ->deletable(false)
                //                     // ->addable(false)
                //             ]),
                //         // history
                        // Forms\Components\Tabs\Tab::make('history_tab')
                        //     ->label(__('tickets/ticket-item.form.tabs.history'))
                        //     ->icon('heroicon-m-rectangle-stack')
                        //     ->badge(2)
                        //     ->schema([
                        //         TableRepeater::make('history')
                        //             ->headers([
                        //                 Header::make('date')->label(__('tickets/ticket-item.form.fields.activities.date')),
                        //                 Header::make('template')->label(__('tickets/ticket-item.form.fields.activities.template')),
                        //             ])
                        //             ->schema([
                        //                 Forms\Components\DatePicker::make('date'),
                        //                 Forms\Components\TextInput::make('title')
                        //             ])
                        //             ->deletable(false)
                        //             ->addable(false)
                        //     ]),
                    // ]),
            ]);
    }
}
