<?php

namespace App\Filament\Resources\TS\TicketItemResource\Forms;

use App\Filament\Components\DepartmentPicker;
use App\Filament\Components\VehiclePicker;
use App\Filament\Resources\TS\TicketItemGroupResource\Forms\TicketItemGroupPicker;
use App\Filament\Resources\TS\TicketItemResource\Forms\ActivityRepeater;
use App\Filament\Resources\TS\TicketResource\Components\MaterialRepeater;
use App\Filament\Resources\TS\TicketResource\Components\ServiceRepeater;
use App\Filament\Resources\TS\TicketResource\RelationManagers\TicketItemRelationManager;
use App\Models\TicketAssignment;
use App\Services\Activity\Activity\WorkService;
use App\Services\TS\ActivityService;
use App\Services\TS\HeaderService;
use App\Services\TS\SubjectService;
use App\States\TS\TicketItem\Closed;
use App\States\TS\TicketItem\Created;
use App\States\TS\TicketItem\InProgress;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Dpb\Package\Activities\Models\TemplateGroup as ActivityTemplateGroup;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketItemGroup;
use Dpb\Package\Tickets\Models\TicketSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;

class TicketItemForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->columns(7)
            ->schema(static::schema());
        // ->schema([

        //     Forms\Components\Section::make('TO DO')
        //         ->description('TODO: pripravujeme'),
        // ]);
    }

    public static function schema(): array
    {
        return [
            // date
            Forms\Components\DatePicker::make('date')
                ->label(__('tickets/ticket-item.form.fields.date'))
                ->columnSpan(1)
                ->default(now()),

            // title
            TicketItemGroupPicker::make('group_id')
                ->getOptionLabelFromRecordUsing(fn(TicketItemGroup $record) => "{$record->code} {$record->title}")
                ->relationship('group', 'title')
                ->columnSpan(2)
                ->live(),
            // Forms\Components\Select::make('group_id')
            //     ->label(__('tickets/ticket-item.form.fields.title'))
            //     // ->options(fn() => ActivityTemplateGroup::has('parent')->pluck('title', 'id'))
            //     ->searchable()
            //     ->preload()
            //     ->live(),

            // assigned to e.g. maintenance group
            Forms\Components\ToggleButtons::make('assigned_to')
                ->label(__('tickets/ticket-item.form.fields.assigned_to'))
                ->columnSpan(2)
                ->options(fn() => MaintenanceGroup::pluck('code', 'id'))
                ->default(function (RelationManager $livewire) {
                    return $livewire->getOwnerRecord()->assignedTo?->id;
                })
                ->inline(),

            // state
            Forms\Components\ToggleButtons::make('state')
                ->label(__('tickets/ticket-item.form.fields.state'))
                ->columnSpan(2)
                ->options(fn() => [
                    Created::$name => __('tickets/ticket-item.states.created'),
                    InProgress::$name => __('tickets/ticket-item.states.in-progress'),
                    Closed::$name => __('tickets/ticket-item.states.closed'),
                ])
                ->inline(),

            // Forms\Components\TextInput::make('title')
            //     ->columnSpan(3)
            //     ->label(__('tickets/ticket-item.form.fields.title')),
            Forms\Components\Textarea::make('description')
                ->label(__('tickets/ticket-item.form.fields.description'))
                ->columnSpanFull(),

            // supervised by

            // activities 
            self::tabsSection()
                ->columnSpan(5),

            // history / comments
            self::historySection()
                ->columnSpan(2),
        ];
    }

    private static function tabsSection()
    {
        return Forms\Components\Tabs::make('all_tabs')
            ->tabs([
                // activities
                Forms\Components\Tabs\Tab::make('activities')
                    ->label(__('tickets/ticket-item.form.tabs.activities'))
                    // ->badge(fn ($record) => $record->activities?->count() ?? 0)
                    ->icon('heroicon-m-wrench')
                    ->schema([
                        Forms\Components\Section::make('TO DO')
                             ->description('TO DO: pripravujeme. Toto bude integrované s fondom pracovného času')
                        // ActivityRepeater::make('activities')
                        //     ->label(__('tickets/ticket-item.form.fields.activities.title'))
                        // ->relationship('activities'),
                    ]),
                // materials
                Forms\Components\Tabs\Tab::make('materials')
                    ->label(__('tickets/ticket-item.form.tabs.materials'))
                    ->icon('heroicon-m-rectangle-stack')
                    // ->badge(fn ($record) => $record->materials?->count() ?? 0)
                    ->schema([
                        Forms\Components\Section::make('TO DO')
                            ->description('TO DO: pripravujeme')
                            ->schema([
                                MaterialRepeater::make('materials')
                                // ->relationship('materials'),
                            ]),
                    ]),
                // services
                Forms\Components\Tabs\Tab::make('services')
                    ->label(__('tickets/ticket-item.form.tabs.services'))
                    ->badge(0)
                    ->icon('heroicon-m-user-group')
                    ->schema([
                        Forms\Components\Section::make('TO DO')
                            ->description('TO DO: pripravujeme')
                            ->schema([
                                ServiceRepeater::make('services')
                                // ->relationship('services'),
                            ]),
                    ])
            ]);
    }
    private static function historySection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('TO DO')
            ->description('TO DO: pripravujeme. ')
            ->schema([
                Forms\Components\Tabs::make('comments_tabs')
                    ->tabs([
                        // comments
                        Forms\Components\Tabs\Tab::make('comments_tab')
                            ->label(__('tickets/ticket-item.form.tabs.comments'))
                            ->badge(3)
                            ->icon('heroicon-m-wrench')
                            ->schema([
                                Forms\Components\TextInput::make('h1')->readOnly()->placeholder('...')->hiddenLabel(),
                                Forms\Components\TextInput::make('h2')->readOnly()->placeholder('...')->hiddenLabel(),
                                Forms\Components\RichEditor::make('body')->disabled()->hiddenLabel(),
                                // ->addable(false)
                            ]),
                        // history
                        Forms\Components\Tabs\Tab::make('history_tab')
                            ->label(__('tickets/ticket-item.form.tabs.history'))
                            ->icon('heroicon-m-rectangle-stack')
                            ->badge(2)
                            ->schema([
                                Forms\Components\TextInput::make('h1')->readOnly()->placeholder('...')->hiddenLabel(),
                                Forms\Components\TextInput::make('h2')->readOnly()->placeholder('...')->hiddenLabel(),
                                Forms\Components\TextInput::make('h3')->readOnly()->placeholder('...')->hiddenLabel(),
                            ]),
                    ]),
            ]);
    }
}
