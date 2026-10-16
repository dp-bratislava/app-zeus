<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\AssignWorkAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\DeleteTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\EditTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\EditTaskDiverseBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\RedirectToIndexAction;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskBatchContext;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatchConfig;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatchTaskSubject;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskSubjectPresenter;
use Dpb\Package\Tasks\Models\TaskItemGroup;
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
                AssignWorkAction::make()
                    ->label(__(static::LANG_PFX . 'actions.assign_work_action'))
                    ->color('warning'),
                // EditTaskBatchAction::make()
                //     ->label(__(static::LANG_PFX . 'actions.edit_action')),
                EditTaskDiverseBatchAction::make()
                    ->label(__(static::LANG_PFX . 'actions.edit_action')),
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

        $tasks = $configuration['tasks'] ?? [];

        $subjectIds = collect($tasks)
            ->pluck('subject_id')
            ->filter()
            ->unique()
            ->values();

        $groupIds = collect($tasks)
            ->flatMap(fn(array $task) => collect($task['task_item_groups'] ?? [])
                ->pluck('task_item_group_id'))
            ->filter()
            ->unique()
            ->values();

        $contractIds = collect($tasks)
            ->flatMap(fn(array $task) => collect($task['task_item_groups'] ?? [])
                ->flatMap(fn(array $group) => collect($group['work'] ?? [])
                    ->pluck('contract_id')))
            ->filter()
            ->unique()
            ->values();

        /*
     * Replace these with your actual bulk lookup APIs.
     * Each should issue a query for all supplied IDs, not one per ID.
     */
        $subjectLabels = static::loadSubjectLabels($tasks, $subjectIds);
        $groupLabels = static::loadTaskItemGroupLabels($groupIds);
        $contractLabels = static::loadContractLabels($contractIds);

        $rows = [];

        foreach ($tasks as $task) {
            $subjectId = (int) $task['subject_id'];
            $subjectKey = ($task['subject_type'] ?? '') . ':' . $subjectId;

            foreach ($task['task_item_groups'] ?? [] as $group) {
                $groupId = (int) $group['task_item_group_id'];

                $personnel = [];

                foreach ($group['work'] ?? [] as $contract) {
                    $contractId = (int) $contract['contract_id'];
                    $quantity = (int) ($contract['quantity'] ?? 1);

                    // dd($contractLabels)    ;

                    $personnel[] = ($contractLabels[$contractId] ?? (string) $contractId)
                        . ($quantity > 1 ? " × {$quantity}" : '');
                }

                $rows[] = [
                    'subject_label' => $subjectLabels[$subjectKey]['label'] ?? (string) $subjectId,
                    'subject_description' => $subjectLabels[$subjectKey]['description'] ?? '',
                    'task_item_group' => $groupLabels[$groupId] ?? (string) $groupId,
                    'personnel' => $personnel,
                    'note' => $task['note'] ?? '',
                ];
            }
        }

        return $rows;
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

    private static function subjectsSection1(): Component
    {
        return Section::make('Subjects')
            ->columnSpanFull()
            ->heading(__(static::LANG_PFX . 'sections.subjects'))
            ->schema([
                RepeatableEntry::make('configuration')
                    ->hiddenLabel()
                    ->table([
                        TableColumn::make(__(static::LANG_PFX . 'entries.subject_label'))->width('100px'),
                        TableColumn::make(__(static::LANG_PFX . 'entries.subject_description')),
                        TableColumn::make(__(static::LANG_PFX . 'entries.task_item_group')),
                        TableColumn::make(__(static::LANG_PFX . 'entries.personel')),
                    ])
                    ->schema([
                        TextEntry::make('subject_label')
                            ->state(fn(Model $record) => (new TaskSubjectPresenter($record->subject))->label()),
                        TextEntry::make('subject_description')
                            ->state(fn(Model $record) => (new TaskSubjectPresenter($record->subject))->description()),
                        TextEntry::make('task_item_group')
                            ->state(fn(Model $record) => $record->taskItemGroup->title),
                        TextEntry::make('personel')
                            ->state(fn() => [
                                '10001 - meno1',
                                '10002 - meno2',
                                '10003 - meno3',
                            ])
                            ->badge(),
                        // TextEntry::make('task_url')
                        //     ->state('url')
                        //     ->url(
                        //         fn($record): string =>
                        //         TaskAssignmentResource::getUrl(
                        //             'edit',
                        //             ['record' => 141009],
                        //         )
                        //     )
                        //     ->openUrlInNewTab(),
                    ])
                    ->extraAttributes(fn($state): array => [
                        'class' => match ($state['status'] ?? null) {
                            default => 'bg-green-50',
                        },
                    ])
            ]);
    }

    private static function workSection(): Component
    {
        return Section::make('Work')
            ->heading(__(static::LANG_PFX . 'sections.work'))
            // ->columns(4)
            ->schema([
                RepeatableEntry::make('work')
                    ->hiddenLabel()
                    ->table([
                        TableColumn::make(__(static::LANG_PFX . 'entries.pid')),
                        TableColumn::make(__(static::LANG_PFX . 'entries.name')),
                        TableColumn::make(__(static::LANG_PFX . 'entries.wtf_operation_title')),
                        TableColumn::make(__(static::LANG_PFX . 'entries.wtf_operation_duration')),
                    ])
                    ->schema([
                        TextEntry::make('pid'),
                        TextEntry::make('name'),
                        TextEntry::make('wtf_operation_title'),
                        TextEntry::make('wtf_operation_duration'),
                    ])
                    ->default([
                        ['pid' => '1', 'name' => 'meno 1'],
                        ['pid' => '2', 'name' => 'meno 2'],
                        ['pid' => '3', 'name' => 'meno 3'],
                    ])
            ]);
    }
}
