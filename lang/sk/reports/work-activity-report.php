<?php

return [
    'list_heading' => 'Zostava - detail prác',
    'table' => [
        'heading' => 'Zostava - detail prác',
        'description' => 'Posledná aktualizácia :latest-sync',
        'columns' => [
            'activity_date' => 'Výkon práce',
            'personal_id' => 'Osobné číslo',
            'full_name' => 'Meno',
            'last_name' => 'Priezvisko',
            'first_name' => 'Meno',
            'department_code' => 'Stredisko',
            'activity_subject_type' => [
                'label' => 'Typ predmetu zákazky',
                'tooltip' => 'Napr.: vozidlo, zastávková tabula, úsek koľajovej trate...',
            ],
            'activity_subject_label' => [
                'label' => 'Predmet zákazky',
                'tooltip' => 'Napr. : kód vozidla, umiestnenie zastávkovej tabule, názov úseku koľajovej trate...',
            ],
            'task_id' => 'ID zákazky',
            'task_author_lastname' => 'Zákazku vytvoril',
            'task_group_title' => 'Typ podzákazky',
            'task_item_group_title' => 'Skupina podzákazky',
            'task_item_author_lastname' => 'Podzákazku vytvoril',
            'activity_title' => 'Názov úkonu',
            'activity_expected_duration' => [
                'label' => 'Norma',
                'tooltip' => 'Predpokladané trvanie úkonu podľa normy',
            ],
            'activity_real_duration' => [
                'label' => 'Výkon',
                'tooltip' => 'Skutočné trvanie úkonu',
            ],
            'activity_is_fulfilled' => 'Splnené',
        ],
        'filters' => [
            'date_from' => 'Dátum od',
            'date_to' => 'Dátum od',
            'department' => 'Stredisko',
            'activity_is_fulfilled' => 'Splnené',
        ],
        'actions' => [
            'expot_csv' => 'Export CSV',
            'expot_excel' => 'Export',
        ],
    ],
    'navigation' => [
        'label' => 'Zostava - detail prác',
        'group' => 'Reporty',
    ],
    'resource' => [
        'model_label' => 'detail prác',
        'plural_model_label' => 'detail prác',
    ],
];
