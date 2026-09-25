<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages\CreateTaskBatch;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages\EditTaskBatch;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages\ListTaskBatches;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Tables\TaskBatchesTable;
use Dpb\MasterPermissionGuard\Concerns\HasResourceGuard;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages\ViewTaskBatch;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchInfolist;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchAccessService;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskBatchResource extends Resource
{
    // use HasResourceGuard;

    protected static ?string $model = TaskBatch::class;

    public static function getModelLabel(): string
    {
        return __('dpb-mod-tasks::task-batch.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dpb-mod-tasks::task-batch.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('dpb-mod-tasks::task-batch.navigation.label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canViewAny(): bool
    {
        return app(TaskBatchAccessService::class)
            ->canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return TaskBatchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaskBatchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskBatchesTable::configure($table);
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
            'index' => ListTaskBatches::route('/'),
            'create' => CreateTaskBatch::route('/create'),
            // 'edit' => EditTaskBatch::route('/{record}/edit'),
            'view' => ViewTaskBatch::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'subjects.subject.model',
                'subjects.subject.codes',
                'subjects.subject.licencePlates',
            ]);
    }
}
