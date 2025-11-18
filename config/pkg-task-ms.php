<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default database table prefix used for package migrations
    |--------------------------------------------------------------------------
    */
    'table_prefix' => 'tms_',

    /*
    |--------------------------------------------------------------------------
    | Default class mapping
    |--------------------------------------------------------------------------
    */
    'classes' => [
        'ticket_state_class' => '\Dpb\Package\Tickets\States\TicketState::class',
        'ticket_item_state_class' => '\Dpb\Package\Tickets\States\TicketItemState::class',
    ],
];
