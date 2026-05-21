<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Report Exporter Service URL
    |--------------------------------------------------------------------------
    |
    | This is the base URL for the external report exporter service.
    | Set this value in your .env file using REPORTS_EXPORTER_URL
    |
    */

    'exporter_url' => env('REPORTS_EXPORTER_URL', 'http://host.docker.internal:8111'),

];
