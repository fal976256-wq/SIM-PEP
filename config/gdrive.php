<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Drive Integration via Google Apps Script
    |--------------------------------------------------------------------------
    |
    | webapp_url: URL hasil deploy GAS Web App (Code.gs)
    | Set di .env: GAS_WEBAPP_URL=https://script.google.com/macros/s/xxxxx/exec
    |
    */
    'webapp_url' => env('GAS_WEBAPP_URL', ''),
];
