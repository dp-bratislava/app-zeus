<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Modules\Tasks\Enums\TaskBatchContext;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\AssignWorkAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\DeleteTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\EditTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\RedirectToIndexAction;
use Dpb\Modules\Tasks\Models\TaskBatch;
use Dpb\Modules\Tasks\Models\TaskBatchTaskSubject;
use Dpb\Modules\Tasks\Services\TaskSubjectPresenter;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class TaskBatchInfolist
{
    private const LANG_PFX = 'dpb-mod-tasks::task-batch.infolists.view.';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                static::headerSection(),
                static::subjectsSection(),
                static::workSection()
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
                EditTaskBatchAction::make()
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
                TextEntry::make('itemGroups.taskItemGroup.title')
                    ->label(__(static::LANG_PFX . 'entries.task_item_group'))
                    ->columnSpan(3)
                    ->size(TextSize::Large)
                    ->badge(),
                TextEntry::make('author.lastname')
                    ->label(__(static::LANG_PFX . 'entries.author')),
                TextEntry::make('created_at')
                    ->label(__(static::LANG_PFX . 'entries.created_at')),
            ]);
    }

    private static function subjectsSection(): Component
    {
        return Section::make('Subjects')
            ->heading(__(static::LANG_PFX . 'sections.subjects'))
            ->schema([
                RepeatableEntry::make('subjects')
                    ->hiddenLabel()
                    ->table([
                        TableColumn::make(__(static::LANG_PFX . 'entries.subject_label')),
                        TableColumn::make(__(static::LANG_PFX . 'entries.subject_description')),
                        TableColumn::make(__(static::LANG_PFX . 'entries.subject_length')),
                        TableColumn::make(__(static::LANG_PFX . 'entries.subject_seats')),
                    ])
                    ->schema([
                        TextEntry::make('subject_label')
                            ->state(fn(TaskBatchTaskSubject $record) => (new TaskSubjectPresenter($record->subject))->label()),
                        TextEntry::make('subject_description')
                            ->state(fn(TaskBatchTaskSubject $record) => (new TaskSubjectPresenter($record->subject))->description()),
                        TextEntry::make('subject_length')
                            ->state(fn(TaskBatchTaskSubject $record) => (new TaskSubjectPresenter($record->subject))->size()),
                        TextEntry::make('subject_seats')
                            ->state(fn(TaskBatchTaskSubject $record) => (new TaskSubjectPresenter($record->subject))->seats()),
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
