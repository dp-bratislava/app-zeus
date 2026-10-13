<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

class TaskBatchConfig
{
    public static function make(
        ?string $name = TaskBatchForm::COMPONENT_NAME_TASK_BATCH_CONFIG
    ): Component {
        $data = app(TaskBatchLookupScopeService::class)->batchableTaskItemGroups();

        $columns = $data
            ->map(fn($btig, $key) => TableColumn::make($btig->label)->wrapHeader())
            ->values()
            ->all();

        $fields = $data
            ->map(
                fn($btig) => (bool) ($btig->is_scalable)
                    ? TextInput::make("groups.{$btig->task_item_group_id}")
                    ->numeric()
                    : Checkbox::make("groups.{$btig->task_item_group_id}")
            )
            ->values()
            ->all();

        return Repeater::make($name)
            ->label(__('dpb-mod-tasks::task-batch.form.fields.task_item_groups'))
            ->addable(false)
            ->deletable(false)
            ->reorderable(false)
            ->cloneable(false)
            ->live()
            ->compact()
            ->itemNumbers()
            ->table([
                TableColumn::make('ID')->width('40px'),
                TableColumn::make('Vozidlo')->width('60px'),
                TableColumn::make('Model'),
                TableColumn::make('Dlzka')->width('40px'),
                TableColumn::make('Sedadla')->width('40px'),
                ...$columns,
                TableColumn::make('Pozn')->width('250px'),
            ])
            ->schema([
                TextInput::make('vehicle_id')
                    ->readOnly(),
                TextInput::make('vehicle_label')
                    ->readOnly(),
                TextInput::make('model')
                    ->readOnly(),
                TextInput::make('length')
                    ->readOnly()
                    ->dehydrated(false),
                TextInput::make('seats')
                    ->readOnly()
                    ->dehydrated(false),
                ...$fields,
                TextInput::make('note')
            ]);
    }
}
