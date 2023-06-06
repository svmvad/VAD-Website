<?php

return [
    'downloads_section' => [
        \Netdust\VAD\Shortcodes\Downloads_shortcode::class, [
            'title'			=> '',
            'description'	=> '',
            'icon'	        => '',
        ]
    ],
    'articles_section' => [
        \Netdust\VAD\Shortcodes\ArticleSection_shortcode::class, [
            'add_section'			=> 1,
        ]
    ],
    'post_filter' => [
        \Netdust\VAD\Shortcodes\PostFilter_shortcode::class, [
            'posttype'			=> 'post',
        ]
    ],
    'user_menu' => [
        \Netdust\VAD\Shortcodes\UserMenu_shortcode::class, [
            'menu'			    => 'Profile',
        ]
    ],
    'test' => [
        \Netdust\VAD\Shortcodes\Simple_shortcode::class, [
            'title'			    => 'Hello World',
        ]
    ],
];