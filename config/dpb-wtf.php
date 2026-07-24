<?php

return [
    'retroactive_edit' => [
        'grace_period_level1' => env('DPB_WTF_GRACE_PERIOD_LEVEL1', '10'),
        'grace_period_level2' => env('DPB_WTF_GRACE_PERIOD_LEVEL2', 'UNLIMITED'),
        'grace_period_default' => env('DPB_WTF_GRACE_PERIOD_DEFAULT', '5'),
    ],
    /*
     * [breakactivity_id => [['department' => ['dep_A' OR 'dep_B] AND 'circuits' => ['cir_A' OR 'cir_B'] AND 'professions' => ['prof_A']]]]
     * e.g.
     * [1 => [
     *      ['departments' => [1], 'circuits'    => [5, 6], 'professions' => [2]],
     *      ['departments' => [8], 'professions' => [9]],
     *      ['circuits'    => [4]]
     * ]
     */
    'break_activity_mappings' => [
        1 => [
            [
                'departments' => [
                    // autobusy
                    334,
                    339,
                    343,
                    // elektricky
                    // 259,
                    // 277,
                    // 278,
                    // // trolejbusy
                    // 270,
                    // 269
                ],
            ],
        ],
    ],
    'filament_pages' => [
        'worktime_schedule_planner_page' => [
            'navigation_label' => 'Zmeny',
            'slug' => 'worktime-schedule-planner',
            'view' => 'dpb-work-time-fund::filament.pages.worktime-schedule-planner-page',
            'navigation_icon' => 'heroicon-o-calendar',
        ],
        'deferred_tasks_management_page' => [
            'navigation_label' => 'Prerušené úlohy',
            'slug' => 'deferred-tasks-management',
            'view' => 'dpb-work-time-fund::filament.pages.deferred-tasks-management-page',
            'navigation_icon' => 'heroicon-o-clock',
        ],
        'operations_category_management_page' => [
            'navigation_label' => 'Kategórie operácií',
            'slug' => 'operations-category-management',
            'view' => 'dpb-work-time-fund::filament.pages.operations-category-management-page',
            'navigation_icon' => 'heroicon-o-folder',
        ],
        'vehicle_report_page' => [
            'navigation_label' => 'Report vozidiel',
            'slug' => 'vehicle-report',
            'view' => 'dpb-work-time-fund::filament.pages.vehicle-report.vehicle-report-page',
            'navigation_icon' => 'heroicon-o-document-chart-bar',
        ],
    ],
    'preferred_shift_colors' => [],
    'work_assignment_generator' => [
        /*
         * Whether to extend long operations to more days
         * if false and more days are selected,
         * generator will create new tasks for each day and tasks will be 'paused'
         */
        'extend_long_operation_to_more_days' => true,
        'fill_available_worktime_per_employee' => true,
    ],
    'color_schema' => [
        'default_shift_colors' => [
            '#f4a6a6',
            '#f7c6a3',
            '#f6e7a1',
            '#d7f2a3',
            '#a8e6b1',
            '#aee7e8',
            '#a9c9f5',
            '#c5b3f7',
            '#e0b3f7',
            '#f7b6d2',
        ],
    ],
    'color-schema' => [
        'activity-record-status' => [
            'operation-official' => [
                0 => 'bg-orange-300',
                1 => 'bg-blue-300',
            ],
            'fulfillment' => [
                -1 => 'bg-gray-200',
                0 => 'bg-red-300',
                1 => 'bg-green-300',
                10 => 'bg-gray-50',
            ],
            'official' => [
                -10 => 'bg-gray-50',
                0 => 'bg-orange-200',
                1 => 'bg-blue-200',
            ],
            // 'official' => [0 => 'bg-yellow-300', 1 => 'bg-sky-400'],
            'gradient' => [
                'official' => [
                    -1 => 'bg-gradient-to-r from-gray-200 from-80% to-sky-400 to-80%',
                    0 => 'bg-gradient-to-r from-red-300 from-80% to-sky-400 to-80%',
                    1 => 'bg-gradient-to-r from-green-300 from-80% to-sky-400 to-80%',
                ],
                'custom' => [
                    -1 => 'bg-gradient-to-r from-gray-200 from-80% to-yellow-300 to-80%',
                    0 => 'bg-gradient-to-r from-red-300 from-80% to-yellow-300 to-80%',
                    1 => 'bg-gradient-to-r from-green-300 from-80% to-yellow-300 to-80%',
                ],
            ],
        ],
    ],
    'activity-record-status' => [
        'icons' => [
            'fulfillment' => [
                -1 => 'heroicon-o-exclamation-triangle',
                0 => 'heroicon-o-x-mark',
                1 => 'heroicon-o-check',
            ],
        ],
    ],
];
