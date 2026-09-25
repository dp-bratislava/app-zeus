<?php

return [
    'create_heading' => 'Vytvoriť zákazky',
    'list_heading' => 'Hromadné zákazky',
    'update_heading' => 'Upraviť zákazky',
    'modals' => [
        'delete_action' => [
            'heading' => 'Odstrániť zákazky',
            'description' => 'Táto akcia odstráni všetky zákazky vrátane zaevidovanej práce',
        ],
    ],
    'form' => [
        'steps' => [
            'tasks' => 'Zákazky',
            'work' => 'Práca',
        ],
        'fields' => [
            'date' => 'Dátum',
            'vehicles' => 'Vozidlá',
            'vehicles_search_prompt' => 'Hľadaj voz',
            'template' => 'Typ ošetrenia',
            'contracts' => 'Zamestnanci',
            'task_groups' => 'Skupina zákazky',
            'task_item_groups' => 'Skupina podzákazky',
            'assigned_to' => [
                'label' => 'Podľa technickej prevádzky',
                'hint' => 'Domovská technická prevádzka',
            ],
            'vehicle_type_id' => [
                'label' => 'Podľa typu',
                'hint' => 'filter podľa typu vozidla',
            ],
            'activity_templates' => 'Činnosti / normy',
        ],
        'sections' => [
            'vehicles' => 'Vozidlá',
            'contracts' => 'Zamestnanci',
            'activity_templates' => 'Činnosti / normy',
        ],
        'validations' => [
            'vehicles_required' => 'Vyberte aspoň jedno vozidlo.',
        ],
    ],
    'table' => [
        'heading' => 'Hromadné zákazky',
        'empty_state_heading' => 'Žiadne hromadné zákazky na zobrazenie',
        'columns' => [
            'date' => 'Dátum',
            'subjects' => 'Vozidilá',
            'subject_count' => 'Počet vozidiel',
            'task' => 'Zákazka',
            'task_group' => 'Skupina zákazky',
            'task_item_group' => 'Skupina podzákazky',
            'author' => 'Zapísal',
            'created_at' => 'Zapísané',
        ],
        'filters' => [
            'date' => 'Dátum',
            'subject' => 'Vozidlá',
            'task_group' => 'Skupina zákazky',
            'task_item_group' => 'Skupina podzákazky',
        ],
        'actions' => [
            'create_action' => 'Vytvoriť',
            'edit_action' => 'Upraviť',
        ],
    ],
    'infolists' => [
        'view' => [
            'sections' => [
                'header' => 'Hlavička',
                'subjects' => 'Vozidlá',
                'work' => 'Práca',
            ],
            'actions' => [
                'back_action' => 'Späť',
                'edit_action' => 'Upraviť',
                'delete_action' => 'Odstrániť',
                'assign_work_action' => 'Pridať prácu',
            ],
            'entries' => [
                'date' => 'Dátum',
                'pid' => 'Osob. č.',
                'name' => 'Meno',
                'subject_label' => 'Č. vozidla',
                'subject_description' => 'Model vozidla',
                'subject_length' => 'Dĺžka v m',
                'subject_seats' => 'Sedadiel',
                'subject_type' => 'Typ vozidla',
                'wtf_operation_title' => 'Norma',
                'wtf_operation_duration' => 'Trvanie',
                'subject_count' => 'Počet vozidiel',
                'task' => 'Zákazka',
                'task_group' => 'Skupina zákazky',
                'task_item_group' => 'Skupina podzákazky',
                'author' => 'Zapísal',
                'created_at' => 'Zapísané',
            ],
        ],
    ],
    'navigation' => [
        'label' => 'Hromadné zákazky',
        // 'group' => 'Kontroly',
    ],
    'resource' => [
        'model_label' => 'Hromadné zákazky',
        'plural_model_label' => 'Hromadné zákazky',
    ],
];
