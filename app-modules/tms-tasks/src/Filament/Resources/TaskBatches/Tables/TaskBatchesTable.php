<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Tables;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\CreateTaskBatchAction;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Factories\TaskBatchContextFactory;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskAssignmentSubjectResolver;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchAccessService;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchConfigMapper;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchRecordScopeService;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskSubjectPresenter;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class TaskBatchesTable
{
    public static function configure(Table $table): Table
    {
        $langTablePfx = 'dpb-mod-tasks::task-batch.table.';
        $langColsPfx = $langTablePfx . 'columns.';

        return $table
            ->heading(__($langTablePfx . 'heading'))
            ->emptyStateHeading(__($langTablePfx . 'empty_state_heading'))
            ->paginated([10, 20, 50, 100])
            ->defaultPaginationPageOption(20)
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn(TaskBatch $record): ?string => match (true) {
                $record->hasTaskWithNoWork() => 'bg-red-200',
                default => null,
            })
            ->modifyQueryUsing(function (
                Builder $query,
                TaskBatchRecordScopeService $scopeSvc
            ): Builder {
                $query = $scopeSvc
                    ->taskBatchScope($query)
                    ->with([
                        'taskGroup:id,title',
                    ]);
                return $query;
            })
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('date')
                    ->label(__($langColsPfx . 'date'))
                    ->date('Y-m-d'),
                TextColumn::make('taskGroup.title')
                    ->label(__($langColsPfx . 'task_group')),
                TextColumn::make('configuration')
                    ->label(__($langColsPfx . 'subjects'))
                    ->state(function (TaskBatch $record, TaskBatchConfigMapper $mapper): array {
                        $configs = $mapper
                            ->map($record->configuration);

                        $references = $configs
                            ->map(fn(TaskBatchTaskConfig $config) => $config->subject)
                            ->unique(
                                fn(TaskSubjectReference $subject) =>
                                "{$subject->type->value}:{$subject->id}"
                            )
                            ->values();

                        $subjects = TaskAssignmentSubjectResolver::resolveMany($references);

                        return TaskSubjectPresenter::labels($subjects)
                            ->unique()
                            ->values()
                            ->all();
                    })
                    ->badge()
                    ->wrap()
                    ->grow()
                    ->size(TextSize::Large),
                TextColumn::make('author.lastname')
                    ->label(__($langColsPfx . 'author')),
                TextColumn::make('created_at')
                    ->label(__($langColsPfx . 'created_at'))
            ])
            ->filters([
                // DateRangeFilter::make('date')
                //     ->label(__('dpb-mod-tasks::task-batch.table.filters.date')),
                self::dateRangeFilter('date'),
                self::taskGroupFilter(),
                // TaskSubjectFilter::make('configuration'),

            ])
            ->recordActions([
                // EditTaskBatchAction::make()
                //     ->label(__('dpb-mod-tasks::task-batch.table.actions.edit_action'))
            ])
            ->headerActions([
                CreateTaskBatchAction::make('create_action')
                    ->label(__('dpb-mod-tasks::task-batch.table.actions.create_action'))
                    ->visible(fn(TaskBatchAccessService $accessSvc) => $accessSvc->canCreate())
            ]);
    }

    private static function dateRangeFilter(string $dateColName): Filter
    {
        return Filter::make('date')
            ->form([
                DatePicker::make('created_from')
                    ->label(__('dpb-mod-tasks::task-batch.table.filters.created_from')),
                DatePicker::make('created_until')
                    ->label(__('dpb-mod-tasks::task-batch.table.filters.created_until'))
            ])
            ->query(function (Builder $query, array $data) use ($dateColName): Builder {
                return $query
                    ->when(
                        $data['created_from'],
                        fn(Builder $query, $date): Builder => $query->whereDate($dateColName, '>=', $date),
                    )
                    ->when(
                        $data['created_until'],
                        fn(Builder $query, $date): Builder => $query->whereDate($dateColName, '<=', $date),
                    );
            });
    }

    /**
     * @todo
     * 
     * need mapping for json filtering
     * 
     * @return SelectFilter
     */
    private static function taskSubjectFilter(): SelectFilter
    {
        return SelectFilter::make('subject')
            ->label(__('dpb-mod-tasks::task-batch.table.filters.subject'))
            ->multiple()
            ->preload()
            ->searchable();
    }

    private static function taskGroupFilter(): SelectFilter
    {
        return SelectFilter::make('taskGroup')
            // return SelectFilter::make('task_group_id')
            ->label(__('dpb-mod-tasks::task-batch.table.filters.task_group'))
            ->relationship(
                'taskGroup',
                'title',
                function (
                    $query,
                    TaskBatchRecordScopeService $scopeSvc
                ) {
                    return $scopeSvc->taskGroupScope($query);
                }
            )
            ->multiple()
            ->preload()
            ->searchable();
    }
}
