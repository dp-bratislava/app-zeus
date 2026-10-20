<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\DatahubSync\Models\Department;
use Dpb\Modules\Tasks\Eligibility\Models\OperationRule;
use Dpb\Package\TaskMS\Models\DepartmentAssignment;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Dpb\WorkTimeFund\Models\Category;
use Dpb\WorkTimeFund\Models\Operation;
use Illuminate\Support\Facades\DB;

class VehicleCleaningMigration implements DataMigration
{
    private const SUBJECT_TYPE = 'vehicle';

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
        $tigCodePfx = 'cistenie_vozidiel.';

        $taskItemGroups = [
            ['code' => $tigCodePfx . 'cistenie_b', 'title' => 'Čistenie B', 'is_scalable' => 0],
            ['code' => $tigCodePfx . 'grafity_a', 'title' => 'Grafity A', 'is_scalable' => 1, 'label' => 'Graf. A'],
            ['code' => $tigCodePfx . 'grafity_b', 'title' => 'Grafity B', 'is_scalable' => 1, 'label' => 'Graf. B'],
            ['code' => $tigCodePfx . 'grafity_c', 'title' => 'Grafity C', 'is_scalable' => 1, 'label' => 'Graf. C'],
            ['code' => $tigCodePfx . 'grafity_externe', 'title' => 'Grafity Ext', 'is_scalable' => 1, 'label' => 'Graf. ext'],
            ['code' => $tigCodePfx . 'podlaha', 'title' => 'Podlaha', 'is_scalable' => 0],
            ['code' => $tigCodePfx . 'okna', 'title' => 'Okná', 'is_scalable' => 0],
            ['code' => $tigCodePfx . 'klimatizacia', 'title' => 'Strop a klimatizácia', 'is_scalable' => 0, 'label' => 'Strop a klíma'],
            ['code' => $tigCodePfx . 'tepovanie_sedadiel', 'title' => 'Tepovanie sedadiel', 'is_scalable' => 0, 'label' => 'Tep'],
            ['code' => $tigCodePfx . 'tepovanie_sedadiel_vodica.suche', 'title' => 'Tepovanie sedadiel vodiča - suchý tep', 'is_scalable' => 0, 'label' => 'Suchý tep SV'],
            ['code' => $tigCodePfx . 'tepovanie_sedadiel_vodica.mokre', 'title' => 'Tepovanie sedadiel vodiča - mokrý tep', 'is_scalable' => 0, 'label' => 'Mokrý tep SV'],
            ['code' => $tigCodePfx . 'mimoriadne', 'title' => 'Mimoriadne', 'is_scalable' => 0, 'label' => 'Mimor.'],
            ['code' => $tigCodePfx . 'biologicke_znecistenie', 'title' => 'Biologické znečistenie', 'is_scalable' => 0, 'label' => 'Bio zn'],
            ['code' => $tigCodePfx . 'znacne_znecistenie', 'title' => 'Značné znečistenie', 'is_scalable' => 0, 'label' => 'Značné zn'],
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
        DB::transaction(function () use ($cleaningDepartmentId, $taskItemGroups, $tigCodePfx) {
            foreach ($taskItemGroups as $tig) {
                $tigId = TaskItemGroup::where('code', '=', $tig['code'])->first()->id;

                DB::table('tms_task_batch_department_tigs')->upsert(
                    [
                        'department_id' => $cleaningDepartmentId,
                        'task_item_group_id' => $tigId,
                        'label' => isset($tig['label']) ? $tig['label'] : $tig['title'],
                        'is_scalable' => $tig['is_scalable'],
                    ],
                    ['department_id', 'task_item_group_id']
                );

                DepartmentAssignment::firstOrCreate([
                    'department_id' => $cleaningDepartmentId,
                    'subject_id' => $tigId,
                    'subject_type' => 'task-item-group',
                ]);
            }

            // bind operations to task item groups and subject conditions
            $rules = [];

            $tigOperationMap = [
                'grafity_a' => 'Čistenie grafitu v interiéri A - malý',
                'grafity_b' => 'Čistenie grafitu v interiéri B - stredný',
                'grafity_c' => 'Čistenie grafitu v interiéri C - veľký',
                'grafity_externe' => 'Čistenie grafitu v exteriéri',
                'podlaha' => 'Umývanie podláh na požiadanie z prevádzky',
                'klimatizacia' => 'Mimoriadne čistenie stropov pri výduchoch',
                'tepovanie_sedadiel' => 'Tepovanie sedadiel',
                'biologicke_znecistenie' => 'Odstraňovanie biologického znečistenia (zvratky, exkrementy, potravinový odpad, apod.)',
                'znacne_znecistenie' => 'Čistenie prilepených ťažko odstrániteľných nečistôť (žuvačky, nálepky, apod.)',
                // 'okna' => '',
                // 'tepovanie_sedadiel_vodica.suche' => '',
                // 'tepovanie_sedadiel_vodica.mokre' => '',
                // 'mimoriadne' => '',
            ];

            // bind operation categories to subject conditions
            $conditionMap = [
                'A-Bus do 12m' => json_encode([
                    'vehicle_type' => [
                        'in' => ['A'],
                    ],
                    'length' => [
                        'lt' => 12
                    ]
                ]),
                'A-Bus nad 12m' => json_encode([
                    'vehicle_type' => [
                        'in' => ['A'],
                    ],
                    'length' => [
                        'gte' => 12
                    ]
                ]),
                'T-Bus 12m' => json_encode([
                    'vehicle_type' => [
                        'in' => ['T'],
                    ],
                    'length' => [
                        'lt' => 12
                    ]
                ]),
                'T-Bus 18m' => json_encode([
                    'vehicle_type' => [
                        'in' => ['T'],
                    ],
                    'length' => [
                        'gte' => 12,
                        'lt' => 18
                    ]
                ]),
                'T-Bus 24m' => json_encode([
                    'vehicle_type' => [
                        'in' => ['T'],
                    ],
                    'length' => [
                        'gte' => 18
                    ]
                ]),
                'Električky' => json_encode([
                    'vehicle_type' => [
                        'in' => ['E'],
                    ]
                ]),
            ];

            foreach ($conditionMap as $categoryTitle => $conditions) {
                $categoryId = Category::where('title', '=', $categoryTitle)
                    ->first()->id;

                foreach ($tigOperationMap as $tigCode => $operationTitle) {
                    $rules[] = [
                        'task_item_group_id' => TaskItemGroup::where('code', '=', $tigCodePfx . $tigCode)
                            ->first()->id,
                        'subject_type' => self::SUBJECT_TYPE,
                        'operation_id' => Operation::where('parent_id', '=', $categoryId)
                            ->whereLike('title', $operationTitle)
                            ->first()->id,
                        'conditions' => $conditions,
                    ];
                }

                $rules = array_merge($rules, $this->cleaningB(
                    $categoryId,
                    $conditions
                ));
            }

            $now = now();

            $rules = array_map(
                fn(array $rule) => [
                    ...$rule,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                $rules,
            );

            OperationRule::insert($rules);
        });
    }

    private function cleaningB(
        int $categoryId,
        string $conditions
    ): array {
        $result = [];

        $tigCleaningBId = TaskItemGroup::where('code', '=', 'cistenie_vozidiel.cistenie_b')->first()->id;

        $categoryCelaningB = Category::where('parent_id', '=', $categoryId)->first()->id;
        $operations = Operation::where('parent_id', '=', $categoryCelaningB)->pluck('id');

        foreach ($operations as $operationId) {
            $result[] = [
                'task_item_group_id' => $tigCleaningBId,
                'subject_type' => self::SUBJECT_TYPE,
                'operation_id' => $operationId,
                'conditions' => $conditions,
            ];
        }
        return $result;
    }
}
