<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\DeleteTaskBatchAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\RedirectToIndexAction;
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
            ]);
    }

    private static function headerSection(): Component
    {
        return Section::make('Header')
            ->heading(fn($record) => $record->date->format('Y-m-d') . " " . $record->taskGroup->title)
            ->headerActions([
                RedirectToIndexAction::make()
                    ->label(__(static::LANG_PFX . 'actions.back_action')),
                EditAction::make()
                    ->label(__(static::LANG_PFX . 'actions.edit_action')),
                DeleteTaskBatchAction::make()
                    ->label(__(static::LANG_PFX . 'actions.delete_action'))
                    ->color('danger'),

            ])
            ->columnSpanFull();
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
