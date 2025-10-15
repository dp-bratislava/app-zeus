<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Components\DepartmentPicker;
use App\Filament\Imports\Fleet\VehicleImporter;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Pages;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\RelationManagers;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Tables\VehicleTable;
use App\Services\Fleet\VehicleService;
use App\StateTransitions\Fleet\Vehicle\DiscardedToInService;
use App\StateTransitions\Fleet\Vehicle\InServiceToDiscarded;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationLabel = 'Vozidlá';
    protected static ?string $pluralModelLabel = 'Vozidlá';
    protected static ?string $ModelLabel = 'Vozidlo';

    public static function getNavigationGroup(): ?string
    {
        return 'Flotila';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('vin')->label('VIN'),
                Forms\Components\TextInput::make('code')->label('Kód'),
                Forms\Components\Select::make('model_id')
                    ->label('')
                    ->relationship('model', 'title')
                    ->preload()
                    ->searchable(),
                DepartmentPicker::make('department')
                    ->getOptionLabelFromRecordUsing(null)
                    ->getSearchResultsUsing(null)
                    ->searchable()
                    ->label('Stredisko')
            ]);
    }

    public static function table(Table $table): Table
    {
        return VehicleTable::make($table);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('tabs')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('technicke parametre')
                            ->schema([
                                Infolists\Components\TextEntry::make('param 1'),
                                Infolists\Components\TextEntry::make('param 2'),
                                Infolists\Components\TextEntry::make('param ...'),
                                Infolists\Components\TextEntry::make('param N'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('poistne udalosti')
                            ->schema([
                                Infolists\Components\TextEntry::make('param 1'),
                                Infolists\Components\TextEntry::make('param 2'),
                                Infolists\Components\TextEntry::make('param ...'),
                                Infolists\Components\TextEntry::make('param N'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('poruchy')
                            ->schema([
                                Infolists\Components\TextEntry::make('param 1'),
                                Infolists\Components\TextEntry::make('param 2'),
                                Infolists\Components\TextEntry::make('param ...'),
                                Infolists\Components\TextEntry::make('param N'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('material')
                            ->schema([
                                Infolists\Components\TextEntry::make('tickets.materials.title'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('STK')
                            ->schema([
                                Infolists\Components\TextEntry::make('param 1'),
                                Infolists\Components\TextEntry::make('param 2'),
                                Infolists\Components\TextEntry::make('param ...'),
                                Infolists\Components\TextEntry::make('param N'),
                            ]),

                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\TicketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
            'view' => Pages\ViewVehicle::route('/{record}'),
        ];
    }
}
