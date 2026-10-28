<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\DeleteTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\EditDiverseTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\EditUniformTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\RedirectToIndexAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages\EditUniformTaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskBatchContext;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchInfolistMapper;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Illuminate\Database\Eloquent\Model;

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
                // EditAction::make()
                //     ->label(__(static::LANG_PFX . 'actions.edit_action')),

                EditUniformTaskBatchAction::make()
                    ->label(__(static::LANG_PFX . 'actions.edit_action'))
                    ->visible(
                        fn(TaskBatch $record): bool =>
                        data_get($record->configuration, 'mode')
                            === TaskBatchMode::Uniform->value
                    ),
                EditDiverseTaskBatchAction::make()
                    ->label(__(static::LANG_PFX . 'actions.edit_action'))
                    ->visible(
                        fn(TaskBatch $record): bool =>
                        data_get($record->configuration, 'mode')
                            === TaskBatchMode::Diverse->value
                    ),
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
                TextEntry::make('configuration.mode')
                    ->label(__(static::LANG_PFX . 'entries.mode'))
                    ->formatStateUsing(fn($record) => TaskBatchMode::tryFrom($record->configuration['mode'])->label()),
                TextEntry::make('author.lastname')
                    ->label(__(static::LANG_PFX . 'entries.author')),
                TextEntry::make('created_at')
                    ->label(__(static::LANG_PFX . 'entries.created_at')),
            ]);
    }

    private static function subjectsSection(): Component
    {
        return Section::make('Subjects')
            ->columnSpanFull()
            ->heading(__(static::LANG_PFX . 'sections.subjects'))
            ->schema([
                RepeatableEntry::make('configuration_rows')
                    ->hiddenLabel()
                    ->state(
                        fn(
                            Model $record,
                            TaskBatchInfolistMapper $mapper,
                        ): array => $mapper->map($record)
                    )
                    ->table([
                        TableColumn::make(
                            __(static::LANG_PFX . 'entries.subject_label')
                        ),
                        TableColumn::make(
                            __(static::LANG_PFX . 'entries.task_item_group')
                        ),
                        TableColumn::make(
                            __(static::LANG_PFX . 'entries.personel')
                        ),
                    ])
                    ->schema([
                        TextEntry::make('subject_label'),
                        TextEntry::make('task_item_group'),
                        TextEntry::make('personnel')
                            ->badge()
                            ->size(TextSize::Large),
                    ]),
            ]);
    }
}
