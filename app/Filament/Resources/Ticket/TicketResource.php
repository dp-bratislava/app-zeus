<?php

namespace App\Filament\Resources\Ticket;

use App\Filament\Components\ContractPicker;
use App\Filament\Components\DepartmentPicker;
use App\Filament\Resources\Ticket\TicketResource\Components\ActivityRepeater;
use App\Filament\Resources\Ticket\TicketResource\Components\MaterialRepeater;
use App\Filament\Resources\Ticket\TicketResource\Components\ServiceRepeater;
use App\Filament\Resources\Ticket\TicketResource\Pages;
use App\Filament\Resources\Ticket\TicketResource\RelationManagers\ActivitiesRelationManager;
use App\Models\Fleet\Vehicle as FleetVehicle;
use App\StateTransitions\Ticket\CreatedToInProgress;
use Dpb\Package\Tickets\Models\Ticket;
use App\Models\TS\Task\TaskStatus;
use App\Services\Activity\Activity\WorkService;
use App\Services\Ticket\ActivityService;
use App\Services\Ticket\HeaderService;
use App\Services\Ticket\SubjectService;
use App\Services\TicketService;
use App\StateTransitions\Ticket\InProgressToCancelled;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Dpb\DatahubSync\Models\Department;
use Dpb\PkgTickets\Models\TicketGroup;
use Dpb\Package\Vehicles\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationLabel = 'Zakázky';
    protected static ?string $pluralModelLabel = 'Zakázky';
    protected static ?string $ModelLabel = 'Zakázka';

    public static function getNavigationGroup(): ?string
    {
        return 'Zakázky';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')->default(now()),
                Forms\Components\Select::make('group_id')
                    ->relationship('group', 'title')
                    ->live(),
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('description'),
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
                            ->schema([
                                ActivityRepeater::make('activities')
                                // ->relationship('activities'),
                            ]),
                        // // materials
                        // Forms\Components\Tabs\Tab::make('materials')
                        //     ->schema([
                        //         MaterialRepeater::make('materials')
                        //             ->relationship('materials'),
                        //     ]),
                        // // services
                        // Forms\Components\Tabs\Tab::make('services')
                        //     ->schema([
                        //         ServiceRepeater::make('services')
                        //             ->relationship('services'),
                        // ])                            
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('date')->date(),
                TextColumn::make('parent.id'),
                TextColumn::make('title'),
                TextColumn::make('description'),
                TextColumn::make('state')
                    ->action(
                        Action::make('select')
                            ->requiresConfirmation()
                            ->action(function (Ticket $record): void {
                                $record->state == 'created'
                                    ? $record->state->transition(new CreatedToInProgress($record, auth()->guard()))
                                    : $record->state->transition(new InProgressToCancelled($record, auth()->guard()->user()));
                            }),
                    ),
                // TextColumn::make('department.code'),
                TextColumn::make('Vozidlo')
                    ->state(function ($record, SubjectService $svc) {
                        return $svc->getSubject($record)?->code;
                    }),
                TextColumn::make('Stredisko')
                    ->state(function (HeaderService $svc, $record) {
                        return $svc->getHeader($record)?->department?->code;
                    }),
                Tables\Columns\TextColumn::make('Normy')
                    ->state(function ($record, ActivityService $svc, WorkService $workService) {
                        $result = $svc->getActivities($record)?->map(function ($activity) use ($workService) {
                            // dd($workService->getWorkIntervals($activity));
                            return $activity->template->title
                                . ' #' . $activity->template->duration
                                . '/' . $workService->getWorkIntervals($activity)?->sum(function($work) {
                                    // return $work;
                                    return $work?->duration;
                                    // return print_r($work?->duration);
                                });
                        });
                        return $result;
                    }),
                // Tables\Columns\TextColumn::make('expenses')
                //     ->state(function ($record) {
                //         $result = $record->materials->sum(function ($material) {
                //             return $material->unit_price * $material->quantity;
                //         });
                //         return $result;
                //     }),

                // Tables\Columns\TextColumn::make('expenses')
                //     ->state(function ($record) {
                //         $materials = $record->materials->sum(function ($material) {
                //             return $material->price;
                //         });
                //         $services = $record->services->sum(function ($service) {
                //             return $service->price;
                //         });
                //         return $materials + $services;
                //     }),
                // Tables\Columns\TextColumn::make('man_minutes')
                //     ->state(function ($record) {
                //         $result = $record->activities->sum('duration');
                //         return $result;
                //     }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // ->after(function (TicketService $ticketService, Department $departmentHdl, array $data, Ticket $record) {
                // ->after(function ($action, $record) {
                //     // $department = $departmentHdl->findOrFail($data['department_id']);
                //     dd($action);
                //     $ticketService->assignDepartment($record, $department);
                // }),
                // Tables\Actions\ReplicateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
