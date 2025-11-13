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

    public static function getModelLabel(): string
    {
        return __('activities/activity-template.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('activities/activity-template.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('activities/activity-template.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('activities/activity-template.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-activities.navigation.activity-template') ?? 999;
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
