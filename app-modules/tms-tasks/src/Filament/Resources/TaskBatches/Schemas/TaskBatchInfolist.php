<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\DeleteTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\EditTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\EditTaskDiverseBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\RedirectToIndexAction;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskBatchContext;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskSubjectPresenter;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskBatchInfolist
{
    private const LANG_PFX = 'dpb-mod-tasks::task-batch.infolists.view.';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                static::headerSection(),
                static::subjectsSection(),
                // static::workSection(),
            ]);
    }

    private static function headerSection(): Component
    {
        return Section::make('Header')
            ->heading(__(static::LANG_PFX . 'sections.header'))
            ->headerActions([
                RedirectToIndexAction::make()
                    ->label(__(static::LANG_PFX . 'actions.back_action')),
                EditAction::make()
                    ->label(__(static::LANG_PFX . 'actions.edit_action')),

                // EditTaskBatchAction::make()
                //     ->label(__(static::LANG_PFX . 'actions.edit_action')),
                // EditTaskDiverseBatchAction::make()
                //     ->label(__(static::LANG_PFX . 'actions.edit_action')),
                DeleteTaskBatchAction::make()
                    ->label(__(static::LANG_PFX . 'actions.delete_action'))
                    ->color('danger'),

            ])
            ->columnSpanFull()
            ->columns(8)
            ->schema([
                TextEntry::make('id'),
                TextEntry::make('date')
                    ->label(__(static::LANG_PFX . 'entries.date'))
                    ->date('Y-m-d'),
                TextEntry::make('batch.context')
                    ->label(__(static::LANG_PFX . 'entries.task_group'))
                    ->formatStateUsing(fn($record) => TaskBatchContext::tryFrom($record->taskGroup->code)->label()),
                // TextEntry::make('itemGroups.taskItemGroup.title')
                //     ->label(__(static::LANG_PFX . 'entries.task_item_group'))
                //     ->columnSpan(3)
                //     ->size(TextSize::Large)
                //     ->badge(),
                TextEntry::make('author.lastname')
                    ->label(__(static::LANG_PFX . 'entries.author')),
                TextEntry::make('created_at')
                    ->label(__(static::LANG_PFX . 'entries.created_at')),
            ]);
    }


    private static function configurationRows(Model $record): array
    {
        $configuration = $record->configuration ?? [];

        if (is_string($configuration)) {
            $configuration = json_decode($configuration, true) ?? [];
        }

        $tasks = collect($configuration['tasks'] ?? []);

        $subjectIds = $tasks
            ->pluck('subject_id')
            ->filter()
            ->unique()
            ->values();

        $groups = $tasks->flatMap(
            fn(array $task) => self::taskItems($task)
        );

        $groupIds = $groups
            ->pluck('task_item_group_id')
            ->filter()
            ->unique()
            ->values();

        $contractIds = $groups
            ->flatMap(fn(array $group) => self::workItems($group))
            ->pluck('contract_id')
            ->filter()
            ->unique()
            ->values();

        $subjectLabels = static::loadSubjectLabels(
            $tasks->all(),
            $subjectIds,
        );

        $groupLabels = static::loadTaskItemGroupLabels($groupIds);
        $contractLabels = static::loadContractLabels($contractIds);

        return $tasks
            ->flatMap(function (array $task) use (
                $subjectLabels,
                $groupLabels,
                $contractLabels,
            ) {
                $subjectId = (int) $task['subject_id'];
                $subjectKey = ($task['subject_type'] ?? '') . ':' . $subjectId;
                $subject = $subjectLabels[$subjectKey] ?? [];

                return collect(self::taskItems($task))
                    ->map(function (array $group) use (
                        $task,
                        $subjectId,
                        $subject,
                        $groupLabels,
                        $contractLabels,
                    ) {
                        $groupId = (int) $group['task_item_group_id'];

                        $personnel = collect(self::workItems($group))
                            ->map(function (array $work) use ($contractLabels) {
                                $contractId = (int) $work['contract_id'];
                                $quantity = (int) ($work['quantity'] ?? 1);

                                return ($contractLabels[$contractId] ?? (string) $contractId)
                                    . ($quantity > 1 ? " × {$quantity}" : '');
                            })
                            ->values()
                            ->all();

                        return [
                            'subject_label' => $subject['label'] ?? (string) $subjectId,
                            'subject_description' => $subject['description'] ?? '',
                            'task_item_group' => $groupLabels[$groupId] ?? (string) $groupId,
                            'personnel' => $personnel,
                            'note' => $task['note'] ?? '',
                        ];
                    });
            })
            ->values()
            ->all();
    }

    /**
     * Normalize the different task-item key names.
     */
    private static function taskItems(array $task): array
    {
        return $task['task_item_groups']
            ?? $task['task_items']
            ?? [];
    }

    /**
     * Normalize work stored directly on a task item or inside operations.
     */
    private static function workItems(array $group): array
    {
        if (isset($group['work'])) {
            return $group['work'];
        }

        return collect($group['operations'] ?? [])
            ->flatMap(fn(array $operation) => $operation['work'] ?? [])
            ->all();
    }


    private static function loadTaskItemGroupLabels(
        Collection $groupIds,
    ): array {
        return TaskItemGroup::query()
            ->whereIn('id', $groupIds)
            ->pluck('title', 'id')
            ->all();
    }

    private static function loadContractLabels(Collection $contractIds): array
    {
        return DB::table('mvw_hr_contract_snapshots')
            ->whereIn('contract_id', $contractIds)
            ->get([
                'contract_id',
                'pid',
                'last_name'
            ])
            ->mapWithKeys(fn($contract) => [
                $contract->contract_id => $contract->pid . ' - ' . $contract->last_name
            ])
            ->all();
    }

    private static function loadSubjectLabels(array $subjects, Collection $subjectIds): array
    {
        return DB::table('mvw_fleet_vehicle_snapshots')
            ->whereIn('vehicle_id', $subjectIds)
            ->get([
                'vehicle_id',
                'code',
                'licence_plate'
            ])
            ->mapWithKeys(fn($vehicle) => [
                'vehicle:' . $vehicle->vehicle_id => [
                    'label' => $vehicle->code ?? $vehicle->licence_plate,
                    'description' => '',
                ],
            ])
            ->all();
    }

    private static function subjectsSection(): Component
    {
        return Section::make('Subjects')
            ->columnSpanFull()
            ->heading(__(static::LANG_PFX . 'sections.subjects'))
            ->schema([
                RepeatableEntry::make('configuration_rows')
                    ->hiddenLabel()
                    ->state(fn(Model $record): array => static::configurationRows($record))
                    ->table([
                        TableColumn::make(
                            __(static::LANG_PFX . 'entries.subject_label')
                        ),
                        // TableColumn::make(
                        //     __(static::LANG_PFX . 'entries.subject_description')
                        // ),
                        TableColumn::make(
                            __(static::LANG_PFX . 'entries.task_item_group')
                        ),
                        TableColumn::make(
                            __(static::LANG_PFX . 'entries.personel')
                        ),
                    ])
                    ->schema([
                        TextEntry::make('subject_label'),
                        // TextEntry::make('subject_description'),
                        TextEntry::make('task_item_group'),
                        TextEntry::make('personnel')
                            // ->listWithLineBreaks(),
                            ->badge()
                            ->size(TextSize::Large),
                    ]),
            ]);
    }
}
