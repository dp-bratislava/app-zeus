<?php

return [
    'list_heading' => 'Zostava - deatil prác',
    'table' => [
        'heading' => 'Zostava - deatil prác',
        'description' => 'ZATIAĽ: Vozy čo majú v štrkáči ako posledné, že sú odstavené',
        'columns' => [
            'activity_date' => 'Výkon práce',
            'personal_id' => 'Osobné číslo',
            'full_name' => 'Meno',
            'last_name' => 'Priezvisko',
            'first_name' => 'Meno',
            'department_code' => 'Stredisko',
            'subject_code' => [
                'label' => 'Predmet zákazky',
                'tooltip' => 'Napr. : kód vozidla, zastávková tabula, úsek koľajovej trate...',
            ],            
            'task_author_lastname' => 'Zákazku vytvoril',
            'task_group_title' => 'Typ podzákazky',
            'task_item_group_title' => 'Skupina podzákazky',
            'task_item_author_lastname' => 'Podzákazku vytvoril',
            'wtf_task_title' => 'Norma',
            'expected_duration' => 'Norma trvanie',
            'real_duration' => 'Skutočné trvanie',
            'is_fulfilled' => 'Splnené',
        ],
        'filters' => [
            'model' => 'Model',
        ],
        'actions' => [
            'expot_csv' => 'Export CSV',
            'expot_excel' => 'Export',
        ],
    ],
    'navigation' => [
        'label' => 'Zostava - deatil prác',
        'group' => 'Reporty',
    ],
    'resource' => [
        'model_label' => 'Správka',
        'plural_model_label' => 'Správka',
    ],
];
