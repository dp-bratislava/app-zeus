<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;

class ContractPicker
{
    public function __construct(
        TaskBatchLookupScopeService $taskBatchLookupScope
    ) {
        throw new \Exception('Not implemented');
    }

    public static function make(?string $name = TaskBatchForm::COL_NAME_CONTRACT_PICKER): Component
    {
        $checkbox = fn(string $name, $options) => CheckboxList::make($name)
            ->options($options)
            ->columnSpan(2);

        $data = [
            'g1' => [1 => 'name 1', 2 =>'name 2'],
            'g2' => [1 => 'name 1', 2 =>'name 2'],
            'g3' => [1 => 'name 1', 2 => 'name 2'],
        ];

        $lists = [];

        foreach ($data as $key => $values) {
            $lists[] = $checkbox($key, $values);
        }
        return Grid::make(1)
            ->columnSpanFull()
            ->columns(12)
            ->schema($lists);
    }
}
