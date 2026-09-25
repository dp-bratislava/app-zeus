<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Tables;

use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Modules\Tasks\Enums\TaskBatchContext;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\CreateTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\EditTaskBatchAction;
use Dpb\Modules\Tasks\Models\TaskBatch;
use Dpb\Modules\Tasks\Models\TaskBatchTaskSubject;
use Dpb\Modules\Tasks\Services\TaskBatchRecordScopeService;
use Dpb\Modules\Tasks\Services\TaskSubjectPresenter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Dpb\Package\TaskMS\UI\Filament\Components\FleetVehiclePicker;
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
                $record->subjects->isEmpty() => 'bg-red-200',
                default => null,
            })
            ->modifyQueryUsing(function (
                Builder $query,
                TaskBatchRecordScopeService $scopeSvc
            ): Builder {
                $query = $scopeSvc
                    ->taskBatchScope($query)
                    ->with([
                        'subjects.subject.codes',
                        'subjects.subject.licencePlates',
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
                TextColumn::make('itemGroups.taskItemGroup.title')
                    ->label(__($langColsPfx . 'task_item_group'))
                    ->size(TextSize::Large)
                    ->badge(),
                TextColumn::make('subjects')
                    ->label(__($langColsPfx . 'subjects'))
                    ->state(function (TaskBatch $record): array {
                        return $record->subjects
                            ->map(
                                fn(TaskBatchTaskSubject $subject) => (new TaskSubjectPresenter($subject->subject))->label()
                            )
                            ->all();
                    })
                    ->badge()
                    ->wrap()
                    ->size(TextSize::Large),
                TextColumn::make('author.lastname')
                    ->label(__($langColsPfx . 'author')),
                TextColumn::make('created_at')
                    ->label(__($langColsPfx . 'created_at'))
            ])
            ->filters([
                DateRangeFilter::make('date')
                    ->label(__('dpb-mod-tasks::task-batch.table.filters.date')),
                SelectFilter::make('task_group_id')
                    ->label(__('dpb-mod-tasks::task-batch.table.filters.task_item_group'))
                    ->relationship('taskGroup', 'title')
                    ->preload()
                    ->multiple()
                    ->searchable(),
                self::taskItemGroupFilter(),
                self::subjectFilter(),

            ])
            ->recordActions([
                // EditTaskBatchAction::make()
                //     ->label(__('dpb-mod-tasks::task-batch.table.actions.edit_action'))
            ])
            ->headerActions([
                CreateTaskBatchAction::make()
                    ->label(__('dpb-mod-tasks::task-batch.table.actions.create_action'))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function subjectFilter(): Filter
    {
        return Filter::make('subject')
            ->schema([
                FleetVehiclePicker::make('subject')
                    ->options([])
                    ->getSearchResultsUsing(null)
                    ->getOptionLabelFromRecordUsing(null)
                    ->searchable()
                    ->multiple()
                    ->label(__('dpb-mod-tasks::task-batch.table.filters.subject')),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['subject'],
                        fn(Builder $query, $subject): Builder => $query->whereHas('subjects', function ($q) use ($subject) {
                            $q->whereMorphedTo(
                                'subject',
                                app(Vehicle::class)->getMorphClass(),
                            )
                                ->whereIn('subject_id', $subject);
                        })
                    );
            });
    }

    private static function taskItemGroupFilter(): SelectFilter
    {
        return SelectFilter::make('itemGroups.taskItemGroup')
            ->label(__('dpb-mod-tasks::task-batch.table.filters.task_item_group'))
            ->relationship('itemGroups.taskItemGroup', 'title')
            ->multiple()
            ->preload()
            ->searchable();
    }
}
