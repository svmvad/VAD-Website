<?php

use \Netdust\Service\Scripts\AdminScript;
use \Netdust\Service\Scripts\FrontScript;

return [

    'common-js' => [
        'handle'      => 'common-js',
        'src'         => app()->js_url() . '/common.js',
        'middlewares' => [FrontScript::class]
    ],

	'admin-js' => [
		'handle'      => 'admin-js',
		'src'         => app()->js_url() . '/admin.js',
		'middlewares' => [AdminScript::class]
	],

    'filter-js' => [
        'handle'      => 'filter-js',
        'src'         => app()->url() . '/app/src/Blocks/filter.js',
        'localized_var'     => 'bs_data'
    ],

    'choises-js' => [
        'handle'      => 'choices-js',
        'src'         => 'https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js',
    ]

];


