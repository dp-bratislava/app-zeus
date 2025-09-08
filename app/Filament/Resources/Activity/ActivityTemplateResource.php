<?php

namespace App\Filament\Resources\Activity;

use App\Filament\Imports\Activity\ActivityTemplateImporter;
use App\Filament\Resources\Activity\ActivityTemplateResource\Pages;
use App\Filament\Resources\Activity\ActivityTemplateResource\RelationManagers;
use Dpb\Packages\Activities\Models\ActivityTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityTemplateResource extends Resource
{
    protected static ?string $model = ActivityTemplate::class;
    protected static ?string $navigationLabel = 'Activity templates';
    protected static ?string $pluralModelLabel = 'Activity templates';
    protected static ?string $ModelLabel = 'Activity template';

    public static function getNavigationGroup(): ?string
    {
        return 'Ticket';
    } 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket.id'),
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('status.title'),
                Tables\Columns\TextColumn::make('template.title')->label('uloha'),
                Tables\Columns\TextColumn::make('template.duration')->label('ocakavane trvanie'),
                Tables\Columns\TextColumn::make('real_duration')
                    ->label('realne trvanie')
                    ->state(function ($record) {
                        $result = $record->activities->sum('duration');
                        return $result;
                    }),
                Tables\Columns\IconColumn::make('template.is_divisible')
                    ->label('delitelna')
                    ->boolean(),
                Tables\Columns\IconColumn::make('template.is_standardised')
                    ->label('normovana')
                    ->boolean(),
                Tables\Columns\IconColumn::make('template.is_catalogised')
                    ->label('katalogizovana')
                    ->boolean(),
                Tables\Columns\TextColumn::make('template.people')
                    ->label('pocet ludi'),
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
