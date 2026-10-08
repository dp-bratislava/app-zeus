<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\DatahubSync\Models\Department;
use Dpb\Package\TaskMS\Models\DepartmentAssignment;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Illuminate\Support\Facades\DB;

class VehicleCleaningMigration implements DataMigration
{
    public function run(): void
    {
        // add vehicle cleaning task group
        $taskGroup = TaskGroup::firstOrCreate(
            ['code' => 'vehicle-cleaning'],
            [
                'title' => 'Čistenie vozidiel',
            ]
        );

        // add vehicle cleaning task item groups
        $taskItemGroups = [
            ['code' => 'cistenie_vozidiel.cistenie_b', 'title' => 'Čistenie B', 'is_scalable' => 0],
            ['code' => 'cistenie_vozidiel.grafity_a', 'title' => 'Grafity A', 'is_scalable' => 1, 'label' => 'Graf. A'],
            ['code' => 'cistenie_vozidiel.grafity_b', 'title' => 'Grafity B', 'is_scalable' => 1, 'label' => 'Graf. B'],
            ['code' => 'cistenie_vozidiel.grafity_c', 'title' => 'Grafity C', 'is_scalable' => 1, 'label' => 'Graf. C'],
            ['code' => 'cistenie_vozidiel.grafity_externe', 'title' => 'Grafity Ext', 'is_scalable' => 1, 'label' => 'Graf. ext'],
            ['code' => 'cistenie_vozidiel.podlaha', 'title' => 'Podlaha', 'is_scalable' => 0],
            ['code' => 'cistenie_vozidiel.okna', 'title' => 'Okná', 'is_scalable' => 0],
            ['code' => 'cistenie_vozidiel.klimatizacia', 'title' => 'Strop a klimatizácia', 'is_scalable' => 0, 'label' => 'Strop a klíma'],
            ['code' => 'cistenie_vozidiel.tepovanie_sedadiel', 'title' => 'Tepovanie sedadiel', 'is_scalable' => 0, 'label' => 'Tep'],
            ['code' => 'cistenie_vozidiel.tepovanie_sedadiel_vodica.suche', 'title' => 'Tepovanie sedadiel vodiča - suchý tep', 'is_scalable' => 0, 'label' => 'Suchý tep SV'],
            ['code' => 'cistenie_vozidiel.tepovanie_sedadiel_vodica.mokre', 'title' => 'Tepovanie sedadiel vodiča - mokrý tep', 'is_scalable' => 0, 'label' => 'Mokrý tep SV'],
            ['code' => 'cistenie_vozidiel.mimoriadne', 'title' => 'Mimoriadne', 'is_scalable' => 0, 'label' => 'Mimor.'],
            ['code' => 'cistenie_vozidiel.biologicke_znecistenie', 'title' => 'Biologické znečistenie', 'is_scalable' => 0, 'label' => 'Bio zn'],
            ['code' => 'cistenie_vozidiel.znacne_znecistenie', 'title' => 'Značné znečistenie', 'is_scalable' => 0, 'label' => 'Značné zn'],
        ];

        TaskItemGroup::upsert(
            collect($taskItemGroups)
                ->map(fn($group) => [
                    'code' => $group['code'],
                    'title' => $group['title'],
                    'automatic_create' => 0,
                    'task_group_id' => $taskGroup->id,
                ])
                ->all(),
            ['code'],
        );

        // assign cleaning group to cleaning department
        $cleaningDepartmentId = Department::where('code', '=', '9486')->first()->id;
        DepartmentAssignment::firstOrCreate([
            'department_id' => $cleaningDepartmentId,
            'subject_id' => $taskGroup->id,
            'subject_type' => 'task-group',
        ]);

        // task batch data
        DB::transaction(function () use ($cleaningDepartmentId, $taskItemGroups) {
            foreach ($taskItemGroups as $tig) {
                $tigId = TaskItemGroup::where('code', '=', $tig['code'])->first()->id;

                DB::table('tms_task_batch_department_tigs')->upsert([
                    'department_id' => $cleaningDepartmentId,
                    'task_item_group_id' => $tigId,
                    'label' => isset($tig['label']) ? $tig['label'] : $tig['title'],
                    'is_scalable' => $tig['is_scalable'],
                ],
                ['department_id', 'task_item_group_id']);

                DepartmentAssignment::firstOrCreate([
                    'department_id' => $cleaningDepartmentId,
                    'subject_id' => $tigId,
                    'subject_type' => 'task-item-group',
                ]);
            }

            // DB::table('task_batch_tig_operations')->insert([
            //     // ...
            // ]);
        });
    }
}
