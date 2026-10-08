<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

class TaskBatchConfig
{
    public static function make(
        ?string $name = TaskBatchForm::COMPONENT_NAME_TASK_BATCH_CONFIG
    ): Component {
        $checkbox = fn(string $name) => Checkbox::make($name)
            ->extraFieldWrapperAttributes([
                'class' => 'flex justify-center size-2',
            ]);

        // $data = [
        //     'cistenie_b' => 'Cistenie B',
        //     'strop'      => 'Strop',
        //     'tsv_mt'     => 'TSV MT',
        //     'tep_sv_st'  => 'Tep SV ST',
        //     'tep_s'      => 'Tep S',
        // ];

        // $data = [
        //     100 => 'Cistenie B',
        //     200 => 'Strop',
        //     300 => 'TSV MT',
        //     400 => 'Tep SV ST',
        //     500 => 'Tep S',
        // ];
        $data = app(TaskBatchLookupScopeService::class)->batchableTaskItemGroups();

        // $columns = collect($data)
        //     // ->map(fn($label, $key) => TableColumn::make($label))
        //     ->map(fn($btig, $key) => TableColumn::make($btig['short_label']))
        //     ->values()
        //     ->all();

        // $fields = collect($data)
        //     // ->map(fn($label, $key) => Checkbox::make('groups.' . $key))
        //     ->map(
        //         fn($btig) => (bool) $btig['is_scalable']
        //             ? TextInput::make("groups.{$btig['id']}")
        //             ->numeric()
        //             : Checkbox::make("groups.{$btig['id']}")                    
        //     )
        //     ->values()
        //     ->all();

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
            // ->default($data)
            // ->formatStateUsing(fn() => $data)
            // ->defaultItems(5)
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
                // TableColumn::make('Cistenie B'),
                // TableColumn::make('Strop'),
                // TableColumn::make('TSV MT'),
                // TableColumn::make('Tep SV ST'),
                // TableColumn::make('Tep S'),
                // TableColumn::make('Dezinf'),
                // TableColumn::make('Schody'),
                // TableColumn::make('GA'),
                // TableColumn::make('GB'),
                // TableColumn::make('GC'),
                // TableColumn::make('MP'),
                // TableColumn::make('ZZ'),
                // TableColumn::make('BZ'),
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
                // Checkbox::make('c1'),
                // $checkbox('cistenie_b'),
                // Checkbox::make('c2'),
                // Checkbox::make('c3'),
                // Checkbox::make('c4'),
                // Checkbox::make('c5'),
                // Checkbox::make('c1'),
                // Checkbox::make('c2'),
                // Checkbox::make('c3'),
                // Checkbox::make('c4'),
                // Checkbox::make('c5'),
                // Checkbox::make('c3'),
                // Checkbox::make('c4'),
                // Checkbox::make('c5'),
                TextInput::make('note')
            ]);
    }
}
