<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Keys & Secrets
    |--------------------------------------------------------------------------
    |
    | This file contains the API keys and secrets for external services.
    | These values serve as fallbacks if the environment variables are not set.
    |
    | WARNING: This file contains sensitive credentials. Ensure it is not
    | publicly accessible if deployed to a public repository.
    |
    */

    'mail' => [
        'mailer' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => 'chisalaluckyk5@gmail.com',
        'password' => 'cflribqhxvyujrzc',
        'encryption' => 'tls',
        'from_address' => 'chisalaluckyk5@gmail.com',
        'from_name' => 'HouseForRent',
    ],

    'google' => [
        'client_id' => '583764156258-3nm6v02ofn7aept734knsf5nf1vtmvvr.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'https://housefor-rent1-main-f94ekk.laravel.cloud/auth/google/callback',
        'maps_key' => 'AIzaSyDH0JpnMofvCFnx9byn6TUm_GV6YW9onZU',
    ],

    'lenco' => [
        'base_url' => 'https://api.lenco.co/access/v2',
        'key' => '155f5e68324c80ea7fe602aae9a40f6a04e20bea91f8eed500f054080bae03c1',
        'secret' => 'pub-7c496e98a882bc37ecfaec59494f84c01583df27533b7c19',
        'webhook_secret' => '2811c74e47c2c7df7e1e88e66ff6746234fc969728348fb8ec4db5909cf422a4',
    ],
];
