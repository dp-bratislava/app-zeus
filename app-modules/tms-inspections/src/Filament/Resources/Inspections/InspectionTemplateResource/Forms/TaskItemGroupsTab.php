<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Forms;

use Dpb\Modules\Inspections\Filament\Components\ToDoSection;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TaskItemGroupsTab
{
    public static function make(): array
    {
        return [
            Forms\Components\CheckboxList::make('templatables.task-item-group')
                ->label(__('dpb-mod-inspections::inspection-template.form.fields.templatables.fields.task_item_groups.label'))
                ->hint(__('dpb-mod-inspections::inspection-template.form.fields.templatables.fields.task_item_groups.hint'))
                ->options(function () {
                    // return TaskItemGroup::byGroupCode('daily-maintenance')
                    return TaskItemGroup::byGroupCode('inspection')
                        ->pluck('title', 'id');
                })
                ->searchable()
                ->bulkToggleable(true)
                ->columnSpanFull()
                ->columns(5),
            // ToDoSection::make()
            //     ->description('zoznam typov podzákazok / normočinností alebo niečoho, čo sa viaže k danej kontrole, takže keď sa vytorí zákazka z kontroly, tak by sa automaticky mohli založiť príslušné podzákazky'),
            // Section::make()
            //     // @todo
            //     ->description('zoznam typov podzákazok / normočinností alebo niečoho, čo sa viaže k danej kontrole, takže keď sa vytorí zákazka z kontroly, tak by sa automaticky mohli založiť príslušné podzákazky')
            //     ->schema([
            //         Forms\Components\ToggleButtons::make('main_task_item_groups')
            //             ->label(__('dpb-mod-inspections::inspection-template.form.fields.task_item_groups'))
            //             ->options(function () {
            //                 return ActivityTemplateGroup::whereNull('parent_id')
            //                     ->get()
            //                     ->mapWithKeys(fn($taskItemGroup) => [
            //                         $taskItemGroup->id => $taskItemGroup->title
            //                     ]);
            //             })
            //             // ->inline()
            //             ->live()
            //             ->columns(4),
            //         //
            //         Forms\Components\CheckboxList::make('task_item_groups')
            //             ->label(__('dpb-mod-inspections::inspection-template.form.fields.task_item_groups'))
            //             ->options(function (Get $get) {
            //                 // return taskItemGroup::get()
            //                 $templateGroupId = $get('main_task_item_groups');
            //                 if ($templateGroupId === null) {
            //                     return [];
            //                 }

            //                 return ActivityTemplate::byTemplateGroupId($templateGroupId)
            //                     ->get()
            //                     ->mapWithKeys(fn($taskItemGroup) => [
            //                         $taskItemGroup->id => $taskItemGroup->title
            //                     ]);
            //             })
            //             ->searchable()
            //             ->bulkToggleable(true)
            //             ->columnSpanFull()
            //             ->columns(6)
            //     ]),
        ];
    }
}
