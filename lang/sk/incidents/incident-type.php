<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť typ udalosti',
        'update_heading' => 'Upraviť typ udalosti: :title',
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'title' => ['label' => 'Názov'],
        ],
    ],
    'table' => [
        'heading' => 'Typy udalostí',
        'empty_state_heading' => 'Žiadne typy udalostí na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'id' => ['label' => 'ID'],
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
        ],
    ],
    'navigation' => [
        'label' => 'Typy udalostí',
        'group' => 'Udalosti',
    ],
    'resource' => [
        'model_label' => 'Typ udalosti',
        'plural_model_label' => 'Typy udalostí',
    ],
];