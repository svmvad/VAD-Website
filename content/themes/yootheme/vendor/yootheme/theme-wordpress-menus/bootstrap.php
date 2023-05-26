<?php

use YOOtheme\Theme\Wordpress\MenuListener;

return [
    'actions' => [
        'init' => [
            MenuListener::class => 'registerMenus',
        ],
    ],
    'filters' => [
        'wp_nav_menu_args' => [
            MenuListener::class => 'filterMenuArgs',
        ],

        'widget_nav_menu_args' => [
            MenuListener::class => ['filterWidgetMenuArgs', 10, 4],
        ],

        'wp_nav_menu_objects' => [
            MenuListener::class => ['getNavMenuObjects', 10, 2],
        ],
    ],
];
