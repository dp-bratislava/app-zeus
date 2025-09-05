<?php

namespace App\Filament\Resources\TS\Issue;

use App\Filament\Resources\TS\Issue\IssueResource\Pages;
use App\Filament\Resources\TS\Issue\IssueResource\RelationManagers;
use App\Models\TS\Issue\Issue;
use App\Models\TS\Issue\Status;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class IssueResource extends Resource
{
    protected static ?string $model = Issue::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Poruchy';
    protected static ?string $pluralModelLabel = 'Poruchy';
    protected static ?string $ModelLabel = 'Porucha';
    public static function canViewAny(): bool
    {
        return false;
    }
    public static function getNavigationGroup(): ?string
    {
        return 'TS';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')->default(Carbon::now()),
                Forms\Components\Select::make('type_id')
                    ->relationship('type', 'title')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('status_id')
                    ->relationship('status', 'title')
                    ->default(Status::default()->first()->id)
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('description'),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('date'),
                Tables\Columns\TextColumn::make('type.title'),
                Tables\Columns\TextColumn::make('status.title'),
                Tables\Columns\TextColumn::make('description'),
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
            'index' => Pages\ListIssues::route('/'),
            'create' => Pages\CreateIssue::route('/create'),
            'edit' => Pages\EditIssue::route('/{record}/edit'),
        ];
    }
}
