<?php

namespace App\Filament\Resources\Activity;

use App\Filament\Resources\Activity\ActivityResource\Pages;
use App\Filament\Resources\Activity\ActivityResource\RelationManagers;
use App\Services\Activity\Activity\TicketService;
use Dpb\Package\Activities\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationLabel = 'Normované činnosti';
    protected static ?string $pluralModelLabel = 'Normované činnosti';
    protected static ?string $ModelLabel = 'Normované činnosti';

    public static function getNavigationGroup(): ?string
    {
        return 'Normy';
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
                Tables\Columns\TextColumn::make('ticket')
                    ->state(function(TicketService $svc, $record) {
                        return $svc->getTicket($record)?->description;
                    }),
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('status.title'),
                Tables\Columns\TextColumn::make('template.title')->label('uloha'),
                Tables\Columns\TextColumn::make('template.duration')->label('ocakavane trvanie'),
                // Tables\Columns\TextColumn::make('real_duration')
                //     ->label('realne trvanie')
                //     ->state(function ($record) {
                //         $result = $record->activities->sum('duration');
                //         return $result;
                //     }),
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
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
