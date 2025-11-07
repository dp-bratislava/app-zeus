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
];
