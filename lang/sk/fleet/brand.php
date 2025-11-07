<?php

return [
    'components' => [
        'picker' => [
            'label' => 'Výrobca vozidla',
            'create_heading' => 'Vytvoriť výrobcu vozidla',
            'update_heading' => 'Upraviť výrobca vozidila: :title',
        ],
    ],    
    'form' => [
        'create_heading' => 'Vytvoriť výrobcu vozidla',
        'update_heading' => 'Upraviť výrobcu vozidla: :title',
        'fields' => [
            'title' => 'Názov',
        ],
    ],
    'table' => [
        'heading' => 'Výrobcovia vozidiel',
        'empty_state_heading' => 'Žiadne výrobcovia vozidiel na zobrazenie',
        'columns' => [
            'title' => 'Názov',
        ]
    ],
    'navigation' => [
        'label' => 'Výrobcovia vozidiel',
        'group' => 'Flotila',
    ],
    'resource' => [
        'model_label' => 'Výrobca vozidiel',
        'plural_model_label' => 'Výrobcovia vozidiel',
    ],
];