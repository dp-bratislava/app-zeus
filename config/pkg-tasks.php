<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default database table prefix used for package migrations
    |--------------------------------------------------------------------------
    */
    'table_prefix' => 'tsk_',

    /*
    |--------------------------------------------------------------------------
    | Default class mapping
    |--------------------------------------------------------------------------
    */
    'classes' => [
        'task_state_class' => '\Dpb\Package\TaskMS\States\Task\Task\TaskState::class',
        'task_item_state_class' => '\Dpb\Package\TaskMS\States\Task\TaskItem\TaskItemState::class',
    ],   

    /*
    |--------------------------------------------------------------------------
    | Navigation items order
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        'task' => 1,
        'task-item' => 2,
        'task-group' => 3,
        'task-item-group' => 4,
        'palce-of-origin' => 5,
    ], 
];
