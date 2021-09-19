<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Single Sign On
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'host'            => env('SSO_HOST', 'https://so.jimmyhowe.com'),
    'client_id'       => env('SSO_CLIENT_ID'),
    'client_secret'   => env('SSO_CLIENT_SECRET'),
    'client_callback' => env('SSO_CLIENT_CALLBACK'),
    'scopes'          => env('SSO_SCOPES'),

];
