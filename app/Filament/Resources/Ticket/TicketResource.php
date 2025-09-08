<?php

namespace App\Filament\Resources\Ticket;

use App\Filament\Components\ContractPicker;
use App\Filament\Components\DepartmentPicker;
use App\Filament\Resources\Ticket\TicketResource\Components\ActivityRepeater;
use App\Filament\Resources\Ticket\TicketResource\Pages;
use App\Models\Fleet\Vehicle as FleetVehicle;
use App\Models\TS\Ticket;
use App\Models\TS\Task\TaskStatus;
use App\Services\TicketService;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Dpb\DatahubSync\Models\Department;
use Dpb\PkgTickets\Models\TicketGroup;
use Dpb\Packages\Vehicles\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Ticket';
    protected static ?string $pluralModelLabel = 'Ticket';
    protected static ?string $ModelLabel = 'Ticket';

    public static function getNavigationGroup(): ?string
    {
        return 'Ticket';
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
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'title', null, true)
                    ->searchable()
                    ->required(false),
                //department
                DepartmentPicker::make('department_id')
                    ->relationship('department', 'title')
                    ->getOptionLabelFromRecordUsing(null)
                    ->getSearchResultsUsing(null)
                    ->searchable()
                    // ->default(function(TicketService $ticketService, $record) {
                    //return $ticketService->getDepartment($record)->id;
                    // return 283;
                    // })
                    // ->dehydrated(false)
                    ->required(),
                // vehicle
                Forms\Components\MorphToSelect::make('subject')
                    ->types([
                        MorphToSelect\Type::make(FleetVehicle::class)
                            ->titleAttribute('code'),                            
                    ])
                    // ->relationship('subject', 'code')
                    // ->options(fn() => Vehicle::pluck('code', 'id'))
                    ->preload()
                    ->searchable(),

                // activities 
                Forms\Components\Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        // activities
                        Forms\Components\Tabs\Tab::make('activities')
                            ->schema([
                                ActivityRepeater::make('activities', '')
                                    ->relationship('activities'),
                                // TableRepeater::make('activities')
                                //     ->relationship('activities')
                                //     ->defaultItems(0)
                                //     ->cloneable()
                                //     ->columnSpan(3)
                                //     ->headers([
                                //         Header::make('date'),
                                //         Header::make('time_from'),
                                //         Header::make('time_to'),
                                //         Header::make('description'),
                                //         Header::make('contract'),
                                //         Header::make('status'),
                                //     ])
                                //     ->schema([
                                //         Forms\Components\DatePicker::make('date')
                                //             ->default(now()),
                                //         // Forms\Components\TextInput::make('duration')
                                //         //     ->numeric()
                                //         //     ->integer()
                                //         //     ->default(60),
                                //         Forms\Components\TimePicker::make('time_from'),
                                //         Forms\Components\TimePicker::make('time_to'),
                                //         Forms\Components\Textarea::make('note'),
                                //         ContractPicker::make('employee_contract_id')
                                //             ->relationship('employeeContract', 'pid')
                                //             ->getOptionLabelFromRecordUsing(null)
                                //             ->getSearchResultsUsing(null)
                                //             ->searchable(),
                                //     ])
                                //     ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get, $set, $livewire) {
                                //         $ticketId = $livewire->record?->id;

                                //         if ($ticketId) {
                                //             $data['ticket_id'] = $ticketId;
                                //         }

                                //         return $data;
                                //     }),

                            ])
                    ])
            ]);

        //                         // activities
        //                         TableRepeater::make('activities')
        //                             ->relationship('activities')
        //                             ->defaultItems(0)
        //                             ->cloneable()
        //                             ->columnSpan(3)
        //                             ->headers([
        //                                 Header::make('date'),
        //                                 Header::make('time_from'),
        //                                 Header::make('time_to'),
        //                                 Header::make('description'),
        //                                 Header::make('contract'),
        //                                 Header::make('status'),
        //                             ])
        //                             ->schema([
        //                                 Forms\Components\DatePicker::make('date')
        //                                     ->default(now()),
        //                                 // Forms\Components\TextInput::make('duration')
        //                                 //     ->numeric()
        //                                 //     ->integer()
        //                                 //     ->default(60),
        //                                 Forms\Components\TimePicker::make('time_from'),
        //                                 Forms\Components\TimePicker::make('time_to'),
        //                                 Forms\Components\Textarea::make('note'),
        //                                 ContractPicker::make('employee_contract_id')
        //                                     ->relationship('employeeContract', 'pid')
        //                                     ->getOptionLabelFromRecordUsing(null)
        //                                     ->getSearchResultsUsing(null)
        //                                     ->searchable(),
        //                             ])
        //                             ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get, $set, $livewire) {
        //                                 $ticketId = $livewire->record?->id;

        //                                 if ($ticketId) {
        //                                     $data['ticket_id'] = $ticketId;
        //                                 }

        //                                 return $data;
        //                             }),
        //                     ]),
        //             ]),
        //         // materials
        //         Forms\Components\Tabs\Tab::make('materials')
        //             ->schema([
        //                 TableRepeater::make('materials')
        //                     ->relationship('materials')
        //                     ->defaultItems(0)
        //                     ->headers([
        //                         Header::make('title'),
        //                         Header::make('unit_price'),
        //                         Header::make('vat'),
        //                         Header::make('quantity'),
        //                     ])
        //                     ->schema([
        //                         Forms\Components\TextInput::make('title'),
        //                         Forms\Components\TextInput::make('unit_price')->numeric(),
        //                         Forms\Components\TextInput::make('vat')->default(23),
        //                         Forms\Components\TextInput::make('quantity')->integer(),
        //                     ]),
        //             ])
        //     ])
        // ]);
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
                TextColumn::make('department.code'),
                TextColumn::make('subject.code'),
                // TextColumn::make('department')
                //     ->state(function (TicketService $ticketService, $record) {
                //         return $ticketService->getDepartment($record)?->code;
                //     }),
                // TextColumn::make('vehicle')
                //     ->state(function (TicketService $ticketService, $record) {
                //         return $ticketService->getVehicle($record)?->code;
                //     }),
                // Tables\Columns\TextColumn::make('expenses')
                //     ->state(function ($record) {
                //         $result = $record->materials->sum(function ($material) {
                //             return $material->unit_price * $material->quantity;
                //         });
                //         return $result;
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
                Tables\Actions\EditAction::make(),
                // ->after(function (TicketService $ticketService, Department $departmentHdl, array $data, Ticket $record) {
                // ->after(function ($action, $record) {
                //     // $department = $departmentHdl->findOrFail($data['department_id']);
                //     dd($action);
                //     $ticketService->assignDepartment($record, $department);
                // }),
                Tables\Actions\ReplicateAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
