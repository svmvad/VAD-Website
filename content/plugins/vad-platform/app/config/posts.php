<?php

return [
    'files' => [
        'type' => 'vad--file', // see register_post_type
        'args' => array(
            'labels'      => array(
                'name'          => __( 'Files', 'vad' ),
                'singular_name' => __( 'File', 'vad' ),
            ),
            'public'      => true,
            'has_archive' => false,
            'show_in_rest' => true,
            'supports' => array('title','thumbnail'),
            'rewrite'     => array( 'slug' => 'dossiers' ),
        )
    ],
    'themes' => [
        'type' => 'vad--theme', // see register_post_type
        'args' => array(
            'labels'      => array(
                'name'          => __( 'Themas', 'vad' ),
                'singular_name' => __( 'Thema', 'vad' ),
            ),
            'public'      => true,
            'has_archive' => 'onze-themas',
            'show_in_rest' => true,
            'supports' => array('title','thumbnail', 'revisions'),
            'rewrite'     => array( 'slug' => 'onze-themas' ),

            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu'=>'vad-website'
        )
    ],
    'researches' => [
        'type' => 'vad--research', // see register_post_type
        'args' => array(
            'labels'      => array(
                'name'          => __( 'Onderzoeken', 'vad' ),
                'singular_name' => __( 'Onderzoek', 'vad' ),
            ),
            'public'      => true,
            'has_archive' => 'onderzoek/databank',

            'rewrite'     => array( 'slug' => 'onderzoek/databank' ),

            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu'=>'vad-website'
        )
    ],
    'vacancies' => [
        'type' => 'vad--vacancy', // see register_post_type
        'args' => array(
            'labels'      => array(
                'name'          => __( 'Vacatures', 'vad' ),
                'singular_name' => __( 'Vacature', 'vad' ),
            ),
            'public'      => true,
            'has_archive' => false,
            'rewrite'     => array( 'slug' => 'vacatures' ),

            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu'=>'vad-website'
        )
    ],
    'employees' => [
        'type' => 'vad--employee', // see register_post_type
        'args' => array(
            'labels'      => array(
                'name'          => __( 'Medewerkers', 'vad' ),
                'singular_name' => __( 'Medewerker', 'vad' ),
            ),
            'public'      => true,
            'has_archive' => false,
            'rewrite'     => array( 'slug' => 'medewerker' ),

            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu'=>'vad-website'
        )
    ]
];