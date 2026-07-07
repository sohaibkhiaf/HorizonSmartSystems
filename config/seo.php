<?php

return [
    'site_name' => env('SEO_SITE_NAME', 'Horizon Smart Systems'),
    'alternate_name' => env('SEO_ALTERNATE_NAME', 'HSS'),

    'locales' => [
        'ar' => [
            'label' => 'Arabic',
            'og_locale' => 'ar_DZ',
            'dir' => 'rtl',
        ],
        'fr' => [
            'label' => 'French',
            'og_locale' => 'fr_DZ',
            'dir' => 'ltr',
        ],
        'en' => [
            'label' => 'English',
            'og_locale' => 'en_US',
            'dir' => 'ltr',
        ],
    ],

    'x_default_locale' => env('SEO_X_DEFAULT_LOCALE', env('APP_FALLBACK_LOCALE', 'fr')),

    'logo' => 'images/hss_logo.png',
    'default_image' => 'images/hss_logo.png',

    'business' => [
        'phone' => '+213561655100',
        'email' => 'horizonsmartsystems@gmail.com',
        'locality' => 'Biskra',
        'country' => 'DZ',
    ],
];
