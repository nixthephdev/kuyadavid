<?php

return [

    /*
    | Where contact-form messages are emailed. Messages are also saved in the
    | admin area, so nothing is lost if email delivery fails.
    */
    'family_email' => env('FAMILY_EMAIL'),

    /*
    | The first admin account, created by `php artisan db:seed`.
    | Leave ADMIN_PASSWORD empty and the seeder will skip it.
    */
    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    // Spam protection for public forms: max submissions per IP per window.
    'form_limit' => [
        'attempts' => 3,
        'decay_seconds' => 600,
    ],

    // Image processing for every upload and for brand/photos.
    'images' => [
        'large' => 1600,
        'small' => 800,
        'max_bytes' => 200 * 1024,
    ],

];
