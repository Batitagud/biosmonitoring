<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Admin SDK (backend - kreait/firebase-php)
    |--------------------------------------------------------------------------
    */
    'credentials' => base_path(env('FIREBASE_CREDENTIALS', 'storage/app/firebase/service-account.json')),

    'database_url' => env('FIREBASE_DATABASE_URL'),

    'project_id' => env('FIREBASE_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Web SDK config (dikirim ke frontend/blade)
    | Dipakai buat Login/Register/Google/Forgot Password via Firebase JS SDK
    |--------------------------------------------------------------------------
    */
    'web' => [
        'apiKey'            => env('FIREBASE_API_KEY'),
        'authDomain'        => env('FIREBASE_AUTH_DOMAIN'),
        'databaseURL'       => env('FIREBASE_DATABASE_URL_WEB'),
        'projectId'         => env('FIREBASE_PROJECT_ID_WEB'),
        'storageBucket'     => env('FIREBASE_STORAGE_BUCKET'),
        'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'appId'             => env('FIREBASE_APP_ID'),
        'measurementId'     => env('FIREBASE_MEASUREMENT_ID'),
    ],

];
