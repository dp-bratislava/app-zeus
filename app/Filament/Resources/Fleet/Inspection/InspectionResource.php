<?php

namespace App\Filament\Resources\Fleet\Inspection;

use App\Filament\Resources\Fleet\Inspection\InspectionResource\Pages;
use App\Filament\Resources\Fleet\Inspection\InspectionResource\RelationManagers;
use Dpb\Package\Fleet\Models\Inspection\Inspection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kontroly';
    protected static ?string $pluralModelLabel = 'Kontroly';
    protected static ?string $ModelLabel = 'Kontrola';

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Fleet';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date_planned_for')->required(),
                Forms\Components\Select::make('vehicle_id')
                    ->relationship('vehicle', 'code')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('inspection_template_id')
                    ->relationship('inspectionTemplate', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('service_group_id')
                    ->relationship('serviceGroup', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('status_id')
                    ->relationship('status', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('distance_traveled')->integer(),
                Forms\Components\TextInput::make('note'),
                Forms\Components\TextInput::make('failures'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('date_planned_for')->date(),
                Tables\Columns\TextColumn::make('vehicle.code'),
                Tables\Columns\TextColumn::make('inspectionTemplate.title'),
                Tables\Columns\TextColumn::make('status.title'),
                Tables\Columns\TextColumn::make('serviceGroup.title'),
                Tables\Columns\TextColumn::make('note'),
                Tables\Columns\TextColumn::make('distance_traveled'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListInspections::route('/'),
            'create' => Pages\CreateInspection::route('/create'),
            'edit' => Pages\EditInspection::route('/{record}/edit'),
        ];
    }
}
