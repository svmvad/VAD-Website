<?php

use YOOtheme\Theme\Wordpress\WpmlListener;

if (!class_exists('SitePress', false)) {
    return [];
}

return [
    'filters' => [
        'wp_get_nav_menus' => [
            WpmlListener::class => ['getNavMenus', 10, 2],
        ],

        'get_terms_args' => [
            WpmlListener::class => 'getTermsArgs',
        ],
    ],
];
