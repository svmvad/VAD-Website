<?php

namespace YOOtheme\Theme\Wordpress\WPML;

use YOOtheme\Builder\BuilderConfig;

return [
    'events' => [
        BuilderConfig::class => [Listener\LoadBuilderConfig::class => ['handle', 20]],
    ],
];
