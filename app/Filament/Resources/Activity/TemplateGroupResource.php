<?php

namespace App\Filament\Resources\Activity;

use App\Filament\Resources\Activity\ActivityTemplateGroupResource\Forms\ActivityTemplateGroupForm;
use App\Filament\Resources\Activity\ActivityTemplateGroupResource\Tables\ActivityTemplateGroupTable;
use App\Filament\Resources\Activity\TemplateGroupResource\Pages;
use Dpb\Package\Activities\Models\TemplateGroup;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class TemplateGroupResource extends Resource
{
    protected static ?string $model = TemplateGroup::class;
    public static function getModelLabel(): string
    {
        return __('activities/activity-template-group.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('activities/activity-template-group.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('activities/activity-template-group.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('activities/activity-template-group.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return ActivityTemplateGroupForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return ActivityTemplateGroupTable::make($table);
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
            'index' => Pages\ListTemplateGroups::route('/'),
            'create' => Pages\CreateTemplateGroup::route('/create'),
            'edit' => Pages\EditTemplateGroup::route('/{record}/edit'),
        ];
    }
}
