<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyExpeditionResource\Forms\DailyExpeditionForm;
use App\Filament\Resources\DailyExpeditionResource\Pages;
use App\Filament\Resources\DailyExpeditionResource\RelationManagers;
use App\Filament\Resources\DailyExpeditionResource\Tables\DailyExpeditionTable;
use App\Models\DailyExpedition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DailyExpeditionResource extends Resource
{
    protected static ?string $model = DailyExpedition::class;

    public static function getModelLabel(): string
    {
        return __('daily-expedition.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('daily-expedition.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('daily-expedition.navigation.label');
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return __('daily-expedition.navigation.group');
    // }
    
    public static function form(Form $form): Form
    {
        return DailyExpeditionForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return DailyExpeditionTable::make($table) ;
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
            'index' => Pages\ListDailyExpeditions::route('/'),
            'custom-index' => Pages\CustomListDailyExpeditions::route('/custom-index'),
            // 'create' => Pages\CreateDailyExpedition::route('/create'),
            'bulk-create' => Pages\BulkCreateDailyExpedition::route('/bulk-create'),
            'bulk-create-2' => Pages\BulkCreateDailyExpedition2::route('/bulk-create-2'),
            'edit' => Pages\EditDailyExpedition::route('/{record}/edit'),
        ];
    }
}
