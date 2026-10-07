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

class VehicleTasksPicker
{
    public static function make(
        ?string $name = TaskBatchForm::COMPONENT_NAME_VEHICLE_TASKS
    ): Component {
        $checkbox = fn(string $name) => Checkbox::make($name)
            ->extraFieldWrapperAttributes([
                'class' => 'flex justify-center size-2',
            ]);

        // $data = [
        //     ['name' => '1000', 'c1' => 1],
        //     ['name' => '1001', 'c1' => 1],
        //     ['name' => '1002', 'c1' => 0],
        // ];

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
                TableColumn::make('ID'),
                TableColumn::make('Vozidlo'),
                TableColumn::make('Cistenie B'),
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
                TableColumn::make('pozn'),
            ])
            ->schema([
                TextInput::make('vehicle_id')
                    ->readOnly(),
                TextInput::make('vehicle_label')
                    ->readOnly(),
                // Checkbox::make('c1'),
                $checkbox('cistenie_b'),
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
