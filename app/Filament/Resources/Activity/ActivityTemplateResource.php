<?php

namespace App\Filament\Resources\Activity;

use App\Filament\Imports\Activity\ActivityTemplateImporter;
use App\Filament\Resources\Activity\ActivityTemplateResource\Pages;
use App\Services\Activity\ActivityTemplate\UnitRateService;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;

class ActivityTemplateResource extends Resource
{
    protected static ?string $model = ActivityTemplate::class;
    protected static ?string $navigationLabel = 'Normy';
    protected static ?string $pluralModelLabel = 'Normy';
    protected static ?string $ModelLabel = 'Norma';

    public static function getNavigationGroup(): ?string
    {
        return 'Normy';
    } 

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\TextInput::make('title'),
    //             //
    //             Forms\Components\Group::make([
    //                 Forms\Components\DatePicker::make('date_from'), 

    //             ]), 
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('duration'),
                Tables\Columns\TextColumn::make('man_minutes'),
                Tables\Columns\IconColumn::make('is_divisible')
                    ->label('delitelna')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_standardised')
                    ->label('normovana')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_catalogised')
                    ->label('katalogizovana')
                    ->boolean(),
                Tables\Columns\TextColumn::make('people')
                    ->label('pocet ludi'),
                Tables\Columns\TextColumn::make('sa1')
                    ->label('sa1')
                    ->state(function($record, UnitRateService $svc) {                        

                        return $svc->getUnitRate($record)?->formatted_rate;
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(ActivityTemplateImporter::class)
                    ->csvDelimiter(';')
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
            'index' => Pages\ListActivityTemplates::route('/'),
            'create' => Pages\CreateActivityTemplate::route('/create'),
            'edit' => Pages\EditActivityTemplate::route('/{record}/edit'),
        ];
    }
}
