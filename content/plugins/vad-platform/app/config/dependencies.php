<?php

use Netdust\Service\Scripts\Script;
use Netdust\Service\Styles\Style;
use Netdust\Service\Pages\Admin\AdminPage;
use Netdust\Service\Posts\Taxonomy;
use Netdust\Service\Posts\Post;

/**
 * define dependencies that need a Registry to help as a Facade and Storage
 */
return [
    Script::class => [
        '\Netdust\Service\Scripts\ScriptRegistry'
    ],
    Style::class => [
        '\Netdust\Service\Styles\StyleRegistry'
    ],
    AdminPage::class => [
        'Netdust\Utils\DependencyRegistry'
    ],
    Taxonomy::class => [
        'Netdust\Utils\DependencyRegistry'
    ],
    Post::class => [
        'Netdust\Utils\DependencyRegistry'
    ]
];