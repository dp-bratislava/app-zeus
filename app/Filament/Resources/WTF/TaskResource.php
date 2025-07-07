<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Components\ContractPicker;
use App\Filament\Components\DepartmentPicker;
use App\Filament\Resources\WTF\TaskResource\Pages;
use App\Filament\Resources\WTF\TaskResource\RelationManagers;
use App\Models\WTF\ActivityStatus;
use App\Models\WTF\Task;
use App\Models\WTF\TaskSubject\Vehicle;
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

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return 'WTF';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')->default(now()),
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
                    ->searchable(),
                // Forms\Components\Select::make('subject')
                //     ->options(fn() => Vehicle::pluck('title', 'id')),
                // standardised activities 
                Forms\Components\Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Std activities')
                            ->schema([

                                Forms\Components\Repeater::make('standardisedActivities')
                                    ->relationship('standardisedActivities')
                                    ->schema([
                                        Forms\Components\Select::make('template_id')
                                            ->relationship('template', 'title')
                                            ->searchable(),

                                        // activities
                                        TableRepeater::make('activities')
                                            ->relationship('activities')
                                            ->headers([
                                                Header::make('date'),
                                                Header::make('time_from'),
                                                Header::make('time_to'),
                                                Header::make('template'),
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
                                                Forms\Components\Select::make('template_id')
                                                    ->relationship('template', 'title')
                                                    ->preload()
                                                    ->searchable(),
                                                ContractPicker::make('employee_contract_id')
                                                    ->relationship('employeeContract', 'pid')
                                                    ->getOptionLabelFromRecordUsing(null)
                                                    ->getSearchResultsUsing(null)
                                                    ->searchable(),
                                                Forms\Components\ToggleButtons::make('status_id')
                                                    ->options(fn() => ActivityStatus::pluck('title', 'id'))
                                                    ->default(ActivityStatus::where('code', '=', 'undone')->first()->id),
                                            ])
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get, $set, $livewire) {
                                                $taskId = $livewire->record?->id;

                                                if ($taskId) {
                                                    $data['task_id'] = $taskId;
                                                }

                                                return $data;
                                            }),
                                    ]),
                            ]),
                        // activities
                        Forms\Components\Tabs\Tab::make('activities')
                            ->schema([
                                TableRepeater::make('activities')
                                    ->relationship('activities')
                                    ->columnSpanFull()
                                    ->headers([
                                        Header::make('date'),
                                        Header::make('time_from'),
                                        Header::make('time_to'),
                                        Header::make('template'),
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
                                        Forms\Components\Select::make('template_id')
                                            ->relationship('template', 'title')
                                            ->preload()
                                            ->searchable(),
                                        ContractPicker::make('employee_contract_id')
                                            ->relationship('employeeContract', 'pid')
                                            ->getOptionLabelFromRecordUsing(null)
                                            ->getSearchResultsUsing(null)
                                            ->searchable(),
                                        Forms\Components\ToggleButtons::make('status_id')
                                            ->options(fn() => ActivityStatus::pluck('title', 'id'))
                                            ->default(ActivityStatus::where('code', '=', 'undone')->first()->id),
                                    ]),
                            ]),
                        // materials
                        Forms\Components\Tabs\Tab::make('materials')
                            ->schema([
                                TableRepeater::make('materials')
                                    ->relationship('materials')
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
                Tables\Columns\TextColumn::make('man_minutes')
                    ->state(function ($record) {
                        $result = $record->activities->sum('duration');
                        return $result;
                    }),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
