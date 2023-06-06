<?php

return [

	'admin-css' => [
		'handle'      => 'admin-css',
		'src'         => app()->css_url() . '/admin.css',
		'name'        => 'admin-css',
		'description' => 'Custom Admin CSS',
        'dependency'  => array( 'learndash-css' ),
		'middlewares' => [
			\Netdust\Service\Styles\AdminStyle::class
		]
	],
    'custom-css' => [
        'handle'      => 'custom-css',
        'src'         => app()->css_url() . '/custom.css',
        'name'        => 'custom-css',
        'description' => 'Custom CSS',
        'middlewares' => [
            \Netdust\Service\Styles\FrontStyle::class
        ]
    ],


    'choise-base-css' => [
        'handle'      => 'choise-base-css',
        'src'         => 'https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/base.min.css',
        'name'        => 'choise-base-css',
        'description' => 'Base Choices CSS',
    ],
    'choise-style-css' => [
        'handle'      => 'choise-style-css',
        'src'         => 'https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css',
        'name'        => 'choise-style-css',
        'description' => 'Style for Choices CSS',
    ],

];
