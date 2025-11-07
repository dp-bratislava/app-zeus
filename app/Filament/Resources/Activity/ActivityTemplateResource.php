<?php

namespace App\Filament\Resources\Activity;

use App\Filament\Imports\Activity\ActivityTemplateImporter;
use App\Filament\Resources\Activity\ActivityTemplateResource\Forms\ActivityTemplateForm;
use App\Filament\Resources\Activity\ActivityTemplateResource\Pages;
use App\Filament\Resources\Activity\ActivityTemplateResource\Tables\ActivityTemplateTable;
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

    public static function form(Form $form): Form
    {
        return ActivityTemplateForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return ActivityTemplateTable::make($table);
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
