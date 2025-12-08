<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default database table prefix used for package migrations
    |--------------------------------------------------------------------------
    */
    'table_prefix' => 'ts_',

    /*
    |--------------------------------------------------------------------------
    | Default class mapping
    |--------------------------------------------------------------------------
    */
    'classes' => [
        'ticket_state_class' => '\Dpb\Package\TaskMS\States\Ticket\Ticket\TicketState::class',
    ],    

    /*
    |--------------------------------------------------------------------------
    | Navigation items order
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        'ticket' => 1,
        'ticket-item' => 2,
        'ticket-group' => 3,
        'ticket-item-group' => 4,
        'ticket-source' => 5,
    ], 
];
