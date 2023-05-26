<?php

namespace YOOtheme\Theme\Wordpress;

return [
    'actions' => [
        'init' => [Listener\AddMenus::class => '@handle'],
    ],

    'filters' => [
        'wp_nav_menu_args' => [Listener\FilterMenuArgs::class => 'handle'],
        'wp_nav_menu_objects' => [Listener\FilterMenuItems::class => ['handle', 10, 2]],
        'widget_nav_menu_args' => [Listener\FilterWidgetMenuArgs::class => ['handle', 10, 4]],
    ],
];
