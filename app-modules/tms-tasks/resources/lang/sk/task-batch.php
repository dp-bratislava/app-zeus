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
        'actions' => [
            'back_to_view_action' => 'Späť',
        ],
        'steps' => [
            'tasks' => 'Zákazky',
            'work' => 'Práca',
        ],
        'tabs' => [
            'subjects' => 'Vozidlá',
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
        'filters' => [
            'maintenance_group' => 'Prevádzka',
            'vehicle_type' => 'Typ vozidla',
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
            'author' => 'Zapísal',
            'created_at' => 'Zapísané',
            'date' => 'Dátum',
            'mode' => 'Režim',
            'subjects' => 'Vozidilá',
            'subject_count' => 'Počet vozidiel',
            'task' => 'Zákazka',
            'task_group' => 'Skupina zákazky',
            'task_item_group' => 'Skupina podzákazky',
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
                'assign_work_action' => 'Pridať prácu',
                'back_action' => 'Späť',
                'delete_action' => 'Odstrániť',
                'edit_action' => 'Upraviť',
            ],
            'entries' => [
                'author' => 'Zapísal',
                'created_at' => 'Zapísané',
                'date' => 'Dátum',
                'name' => 'Meno',
                'mode' => 'Režim',
                'personel' => 'Ľudia',
                'pid' => 'Osob. č.',
                'subject_count' => 'Počet vozidiel',
                'subject_description' => 'Model vozidla',
                'subject_label' => 'Č. vozidla',
                'subject_length' => 'Dĺžka v m',
                'subject_seats' => 'Sedadiel',
                'subject_type' => 'Typ vozidla',
                'task' => 'Zákazka',
                'task_group' => 'Skupina zákazky',
                'task_item_group' => 'Skupina podzákazky',
                'wtf_operation_duration' => 'Trvanie',
                'wtf_operation_title' => 'Norma',
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
