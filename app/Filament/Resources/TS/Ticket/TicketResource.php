<?php

namespace App\Filament\Resources\TS\Ticket;

use App\Filament\Components\ContractPicker;
use App\Filament\Components\DepartmentPicker;
use App\Filament\Resources\TS\Ticket\TicketResource\Pages;
use App\Filament\Resources\TS\Ticket\TicketResource\RelationManagers;
use App\Models\BM\Building;
use App\Models\Fleet\Vehicle as FleetVehicle;
use App\Models\TS\Ticket\Ticket;
use App\Models\TS\Task\TaskStatus;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Zákazky 2';
    protected static ?string $pluralModelLabel = 'Zákazky 2';
    protected static ?string $ModelLabel = 'Zákazka 2';

    public static function getNavigationGroup(): ?string
    {
        return 'TS';
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
                    ->required(),
                // Forms\Components\Select::make('vehicles')
                //     ->relationship('vehicles', 'code')
                //     ->options(fn() => FleetVehicle::pluck('code', 'id'))
                //     ->multiple()
                //     ->searchable()
                //     ->visible(function($get) {
                //         $ticketGroup = TicketGroup::find($get('group_id'))?->code;
                //         return $ticketGroup === 'fleet';
                //     }),
                // Forms\Components\Select::make('buildings')
                //     ->relationship('buildings', 'code')
                //     ->options(fn() => Building::pluck('title', 'id'))
                //     ->multiple()
                //     ->searchable()
                //     ->visible(function($get) {
                //         $ticketGroup = TicketGroup::find($get('group_id'))?->code;
                //         return $ticketGroup === 'building_management';
                //     }),                    

                // standardised activities 
                Forms\Components\Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        // standardised activities
                        Forms\Components\Tabs\Tab::make('tasks')
                            ->schema([
                                Forms\Components\Repeater::make('tasks')
                                    ->columns(3)
                                    ->relationship('tasks')
                                    ->defaultItems(0)
                                    ->schema([
                                        Forms\Components\DatePicker::make('date')
                                            ->default(now())
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('task_template_id')
                                            ->relationship('template', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(1),
                                        Forms\Components\ToggleButtons::make('status_id')
                                            ->options(fn() => TaskStatus::pluck('title', 'id'))
                                            ->default(TaskStatus::where('code', '=', 'new')->first()->id)
                                            ->inline()
                                            ->columnSpan(1),

                                        // activities
                                        TableRepeater::make('activities')
                                            ->relationship('activities')
                                            ->defaultItems(0)
                                            ->cloneable()
                                            ->columnSpan(3)
                                            ->headers([
                                                Header::make('date'),
                                                Header::make('time_from'),
                                                Header::make('time_to'),
                                                Header::make('description'),
                                                Header::make('contract'),
                                                Header::make('status'),
                                            ])
                                            ->schema([
                                                Forms\Components\DatePicker::make('date')
                                                    ->default(now()),
                                                // Forms\Components\TextInput::make('duration')
                                                //     ->numeric()
                                                //     ->integer()
                                                //     ->default(60),
                                                Forms\Components\TimePicker::make('time_from'),
                                                Forms\Components\TimePicker::make('time_to'),
                                                Forms\Components\Textarea::make('note'),
                                                ContractPicker::make('employee_contract_id')
                                                    ->relationship('employeeContract', 'pid')
                                                    ->getOptionLabelFromRecordUsing(null)
                                                    ->getSearchResultsUsing(null)
                                                    ->searchable(),
                                            ])
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get, $set, $livewire) {
                                                $ticketId = $livewire->record?->id;

                                                if ($ticketId) {
                                                    $data['ticket_id'] = $ticketId;
                                                }

                                                return $data;
                                            }),
                                    ]),
                            ]),
                        // materials
                        Forms\Components\Tabs\Tab::make('materials')
                            ->schema([
                                TableRepeater::make('materials')
                                    ->relationship('materials')
                                    ->defaultItems(0)
                                    ->headers([
                                        Header::make('title'),
                                        Header::make('unit_price'),
                                        Header::make('vat'),
                                        Header::make('quantity'),
                                    ])
                                    ->schema([
                                        Forms\Components\TextInput::make('title'),
                                        Forms\Components\TextInput::make('unit_price')->numeric(),
                                        Forms\Components\TextInput::make('vat')->default(23),
                                        Forms\Components\TextInput::make('quantity')->integer(),
                                    ]),
                            ])
                    ])
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
                TextColumn::make('department.code'),
                Tables\Columns\TextColumn::make('expenses')
                    ->state(function ($record) {
                        $result = $record->materials->sum(function ($material) {
                            return $material->unit_price * $material->quantity;
                        });
                        return $result;
                    }),
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
