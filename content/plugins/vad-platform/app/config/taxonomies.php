<?php

return [
    'article_subject' => [
        'post_type'   => 'post',
        'id'          => 'onderwerp',
        'description' => 'What is the subject',
        'name'        => 'Onderwerp',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_in_menu' => false,
            'show_in_rest' => true,
        ],
    ],
    'article_sector' => [
        'post_type'   => 'post',
        'id'          => 'sector',
        'description' => 'What is the sector',
        'name'        => 'Sector',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_in_menu' => false,
            'show_in_rest'=> true,
        ],
    ],
    'article_audience' => [
        'post_type'   => 'post',
        'id'          => 'doelgroep',
        'description' => 'What is the audience',
        'name'        => 'Doelgroep',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_in_menu' => false,
            'show_in_rest'=> true,
        ],
    ],
    'article_product' => [
        'post_type'   => 'post',
        'id'          => 'thema',
        'description' => 'What is the product',
        'name'        => 'Product',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_in_menu' => false,
            'show_in_rest' => true,
        ],
    ],
    'filetype' => [
        'post_type'   => 'vad--file',
        'id'          => 'filetype',
        'description' => 'What type of file is this',
        'name'        => 'Filetypes',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
        ],
    ],
    'method' => [
        'post_type'   => 'vad--research',
        'id'          => 'method',
        'description' => 'What method is used',
        'name'        => 'Methods',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            // 'show_ui' => false,
            // 'meta_box_cb' => false,
        ],
    ],
    'type' => [
        'post_type'   => 'vad--research',
        'id'          => 'type',
        'description' => 'What type of research is this',
        'name'        => 'Types',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            // 'show_ui' => false,
            // 'meta_box_cb' => false,
        ],
    ],
    'financed' => [
        'post_type'   => 'vad--research',
        'id'          => 'financed',
        'description' => 'Who financed the research',
        'name'        => 'Financed',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            // 'show_ui' => false,
            // 'meta_box_cb' => false,
        ],
    ],
    'substance' => [
        'post_type'   => 'vad--research',
        'id'          => 'substance',
        'description' => 'About what product / substance',
        'name'        => 'Research Products / Substance',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            // 'show_ui' => false,
            // 'meta_box_cb' => false,
        ],
    ],
    'discipline' => [
        'post_type'   => 'vad--research',
        'id'          => 'discipline',
        'description' => 'In what discipline',
        'name'        => 'Discipline',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            // 'show_ui' => false,
            // 'meta_box_cb' => false,
        ],
    ],
    'region' => [
        'post_type'   => 'vad--research',
        'id'          => 'region',
        'description' => 'In what region',
        'name'        => 'Regions',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            // 'show_ui' => false,
            // 'meta_box_cb' => false,
        ],
    ],
    'status' => [
        'post_type'   => 'vad--research',
        'id'          => 'status',
        'description' => 'What is the status',
        'name'        => 'Status',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
           // 'show_ui' => false,
           // 'meta_box_cb' => false,
        ],
    ],

    /*
    'doorverwijsgids_themas'  => [
        'post_type'   => ['vad--hulpverlening, vad--vroeginter, vad--preventie'],
        'id'          => 'doorverwijsgids_themas',
        'description' => 'Rond welke themas werken ze',
        'name'        => 'Themas',
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'vroeginterventie_doelgroep'  => [
        'post_type'   => 'vad--vroeginter',
        'id'          => 'vroeginterv_doelgroep',
        'description' => 'Rond welke doelgroepen werken ze',
        'name'        => "Doelgroepen",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'vroeginterventie_aanbod'  => [
        'post_type'   => 'vad--vroeginter',
        'id'          => 'vroeginterv_aanbod',
        'description' => 'Welk aanbod hebben ze naar doelgroepen',
        'name'        => "Aanbod",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => true,
            'meta_box_cb' => true,
        ],
    ],
    'preventie_problem'  => [
        'post_type'   => 'vad--preventie',
        'id'          => 'preventie_problem',
        'description' => 'Problematiek',
        'name'        => "Problematiek",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'preventie_sector'  => [
        'post_type'   => 'vad--preventie',
        'id'          => 'preventie_sector',
        'description' => 'In welke sectoren werken ze',
        'name'        => "Sector",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'preventie_doelgroep'  => [
        'post_type'   => 'vad--preventie',
        'id'          => 'preventie_doelgroep',
        'description' => 'Voor welke doelgroepen hebben ze een aanbod',
        'name'        => "Doelgroep",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'hulpverlening_leeftijd'  => [
        'post_type'   => 'vad--hulpverlening',
        'id'          => 'hulp_leeftijd',
        'description' => 'Voor welke leeftijden',
        'name'        => "Leeftijd",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'hulpverlening_ambulant'  => [
        'post_type'   => 'vad--hulpverlening',
        'id'          => 'hulp_ambulant',
        'description' => 'amulante zorg',
        'name'        => "Ambulant",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'hulpverlening_semi_residentieel'  => [
        'post_type'   => 'vad--hulpverlening',
        'id'          => 'hulp_semi_residentieel',
        'description' => 'semi residentieel',
        'name'        => "Semi-residentieel",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'hulpverlening_residentieel'  => [
        'post_type'   => 'vad--hulpverlening',
        'id'          => 'hulp_residentieel',
        'description' => 'residentieel',
        'name'        => "Residentieel",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'hulpverlening_omgeving'  => [
        'post_type'   => 'vad--hulpverlening',
        'id'          => 'hulp_omgeving',
        'description' => 'Omgeving',
        'name'        => "Omgeving",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'hulpverlening_welzijn_en_advies'  => [
        'post_type'   => 'vad--hulpverlening',
        'id'          => 'hulp_welzijn_en_advies',
        'description' => 'Welzijn en advies',
        'name'        => "Welzijn en advies",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'hulpverlening_behandeling_therapie'  => [
        'post_type'   => 'vad--hulpverlening',
        'id'          => 'hulp_behandeling_therapie',
        'description' => 'Behandeling/Therapie',
        'name'        => "Behandeling/Therapie",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],
    'hulpverlening_overige_werkvorm'  => [
        'post_type'   => 'vad--hulpverlening',
        'id'          => 'hulp_overige_werkvorm',
        'description' => 'Overige werkvorm',
        'name'        => "Overige werkvorm",
        'args'        => [                              // Default atts. See register_taxonomy
            'public' => true,
            'show_ui' => false,
            'meta_box_cb' => false,
        ],
    ],*/

    'woocommerce_type'  => [
        'post_type'   => 'product',
        'id'          => 'cat_type',
        'description' => 'Wat voor type product is het',
        'name'        => "Type",
        'args'        => [                              // Default atts. See register_taxonomy
            'show_ui'           => true,
            'show_admin_column' => false,
            'query_var'         => true,
        ],
    ],
    'woocommerce_product'  => [
        'post_type'   => 'product',
        'id'          => 'cat_product',
        'description' => 'Over welke producten gaat het',
        'name'        => "Product",
        'args'        => [                              // Default atts. See register_taxonomy
            'show_ui'           => true,
            'show_admin_column' => false,
            'query_var'         => true,
        ],
    ],
    'woocommerce_sector'  => [
        'post_type'   => 'product',
        'id'          => 'cat_sector',
        'description' => 'Voor welke sectoren is het',
        'name'        => "Sector",
        'args'        => [                              // Default atts. See register_taxonomy
            'show_ui'           => true,
            'show_admin_column' => false,
            'query_var'         => true,
        ],
    ],
    'woocommerce_doelgroep'  => [
        'post_type'   => 'product',
        'id'          => 'cat_doelgroep',
        'description' => 'Voor welke doelgroepen is het',
        'name'        => "Doelgroepen",
        'args'        => [                              // Default atts. See register_taxonomy
            'show_ui'           => true,
            'show_admin_column' => false,
            'query_var'         => true,
        ],
    ],


];