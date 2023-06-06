<?php

return [
    'downloads-block' => [
        \Netdust\VAD\Blocks\VAD_Downloads_block::class, [
            'name'				=> 'downloads_block',
            'title'				=> 'Verdiepende downloads',
            'description'		=> 'Ontdek hier al het gerelateerde VAD-materiaal. Zowel voor professionals, als voor jongeren, ouders, mensen die gebruiken en hun partners of kinderen, ….'
        ]
    ],
    'related-articles-block' => [
        \Netdust\VAD\Blocks\VAD_RelatedArticlesSection_block::class, [
            'name'				=> 'related_articles_block',
            'title'				=> 'Artikel Related',
            'description'		=> 'description'
        ]
    ],
    'article-intro-block' => [
        \Netdust\VAD\Blocks\VAD_Intro_block::class, [
            'name'				=> 'block-intro-panel',
            'title'				=> 'Artikel Intro',
            'description'		=> 'description'
         ]
    ],
    'article-content-block' => [
        \Netdust\VAD\Blocks\VAD_Section_block::class, [
            'name'				=> 'block-section',
            'title'				=> 'Artikel Sectie',
            'description'		=> 'description'
        ]
    ],
    'article-quote-block' => [
        \Netdust\VAD\Blocks\VAD_QuoteSection_block::class, [
            'name'				=> 'block-quote-section',
            'title'				=> 'Artikel Quote',
            'description'		=> 'description'
        ]
    ],

    'post-filter' => [
        \Netdust\VAD\Blocks\VAD_PostFilter_block::class, [
            'name'				=> 'filter_block',
            'title'				=> 'Post Filter',
            'description'		=> 'description'
        ]
    ]
];