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
        'ticket_state_class' => '\App\States\TS\Ticket\TicketState::class',
        'ticket_item_state_class' => '\App\States\TS\TicketItem\TicketItemState::class',
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
