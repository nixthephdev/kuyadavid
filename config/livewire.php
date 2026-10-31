<?php

// Only the options that differ from Livewire's defaults.
return [

    // Admin full-page components use the admin layout.
    'layout' => 'layouts.admin',

    // Livewire + Alpine are bundled by Vite in resources/js/app.js, so Alpine
    // is available on every page (copy buttons, menu) — not only Livewire ones.
    'inject_assets' => false,

    'temporary_file_upload' => [
        'disk' => 'local',
        'rules' => ['required', 'file', 'max:5120'],
        'directory' => null,
        'middleware' => 'throttle:30,1',
        'preview_mimes' => ['png', 'jpg', 'jpeg', 'webp'],
        'max_upload_time' => 5,
        'cleanup' => true,
    ],

];
