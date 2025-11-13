<?php

namespace App\Filament\Resources\Activity;

use App\Filament\Resources\Activity\ActivityResource\Pages;
use App\Filament\Resources\Activity\ActivityResource\RelationManagers;
use App\Filament\Resources\Activity\ActivityResource\Tables\ActivityAssignmentTable;
use App\Filament\Resources\Activity\ActivityResource\Tables\ActivityTable;
use App\Models\ActivityAssignment;
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
    protected static ?string $model = ActivityAssignment::class;

    public static function getModelLabel(): string
    {
        return __('activities/activity.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('activities/activity.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('activities/activity.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('activities/activity.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-activities.navigation.activity') ?? 999;
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
        return ActivityAssignmentTable::make($table);
        // return ActivityTable::make($table);
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
