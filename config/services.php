<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'yelp' => [
        'api_key' => env( 'YELP_API_KEY' ),
        'host' => env( 'YELP_HOST', 'api.yelp.com' ),
        'zip_codes' => [
            43002, 43004, 43016, 43017, 43026, 43035, 43054, 43065, 43081,
            43082, 43085, 43119, 43201, 43202, 43203, 43204, 43205, 43206,
            43207, 43210, 43211, 43212, 43213, 43214, 43215, 43219, 43220,
            43221, 43222, 43223, 43224, 43227, 43228, 43229, 43230, 43231,
            43235, 43240
        ]
    ],

];
