<?php

return [
    'title' => '',
    'header' => 'Prehľady',
    'widgets' => [
        'vehicles_by_state' => [
            'table' => [
                'heading' => 'Vozidlá podľa stavu',
                'empty_state_heading' => 'Žiadne vozidlá na zobrazenie',
                'columns' => [
                    'state' => 'Stav',
                    'total' => 'Spolu',
                    'model' => 'Model',

                ]
            ],
        ],            
        'vehicles_by_maintenance_group' => [
            'table' => [
                'heading' => 'Správka podľa prevádzok',
                'empty_state_heading' => 'Žiadne vozidlá na zobrazenie',
                'columns' => [
                    'state' => 'Stav',
                    'total' => 'Spolu',
                    'model' => 'Model',
                    'maintenance_group' => 'Technická prevádzka',
                ]
            ],
        ],
        'vehicles_by_model' => [
            'table' => [
                'heading' => 'Správka podľa modelu',
                'empty_state_heading' => 'Žiadne vozidlá na zobrazenie',
                'columns' => [
                    'state' => 'V správke',
                    'total' => 'Spolu',
                    'model' => 'Model',
                ]
            ],
        ],        
    ],
];
