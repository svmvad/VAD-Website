<?php

namespace YOOtheme\Theme\Wordpress;

use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Theme\SystemCheck as BaseSystemCheck;
use YOOtheme\Theme\Updater;
use YOOtheme\View;

require __DIR__ . '/supports.php';
require __DIR__ . '/functions.php';

return [
    'config' => [
        'app' => [
            'isCustomizer' => is_customize_preview(),
        ],
    ],

    'theme' => function (Config $config) {
        return $config->loadFile(Path::get('./config/theme.json'));
    },

    'events' => [
        'app.request' => [
            SystemListener::class => 'checkPermission',
        ],

        'url.resolve' => [
            UrlListener::class => 'routeQueryParams',
        ],

        'theme.init' => [
            ThemeListener::class => 'themeInit',
        ],
    ],

    'actions' => [
        // @link https://developer.wordpress.org/reference/hooks/after_setup_theme/
        'after_setup_theme' => [
            ThemeLoader::class => 'setupTheme',
        ],

        // @link https://developer.wordpress.org/reference/hooks/wp_loaded/
        'wp_loaded' => [
            ThemeLoader::class => 'initTheme',
            UpgradeListener::class => 'initUpdate',
        ],

        'wp_head' => [
            ThemeListener::class => ['addScript', 20],
        ],

        'get_header' => [
            ThemeListener::class => 'onHeader',
        ],

        'wp_enqueue_scripts' => [
            ThemeListener::class => 'addJQuery',
            CommentListener::class => 'addScript',
        ],

        'customize_register' => [
            CustomizerListener::class => 'initConfig',
        ],

        'customize_controls_init' => [
            CustomizerListener::class => 'addAssets',
        ],

        'init' => [
            ChildThemeListener::class => 'initConfig',
        ],

        'after_switch_theme' => [
            ChildThemeListener::class => 'copyConfig',
        ],

        'template_include' => [
            ThemeListener::class => 'includeTemplate',
        ],

        'comment_form_after' => [
            CommentListener::class => 'removeNovalidate',
        ],

        'wp_prepare_themes_for_js' => [
            UpgradeListener::class => 'disableAutoUpdate',
        ],
    ],

    'filters' => [
        'upload_mimes' => [
            ThemeListener::class => 'addSvg',
        ],

        'wp_check_filetype_and_ext' => [
            ThemeListener::class => ['addSvgType', 10, 4],
        ],

        'site_icon_meta_tags' => [
            ThemeListener::class => 'filterMetaTags',
        ],

        'post_gallery' => [
            PostListener::class => ['filterGallery', 10, 3],
        ],

        'comment_reply_link' => [
            CommentListener::class => 'filterReplyLink',
        ],

        'cancel_comment_reply_link' => [
            CommentListener::class => 'filterCancelLink',
        ],

        'get_comment_author_link' => [
            CommentListener::class => 'filterAuthorLink',
        ],
    ],

    'extend' => [
        View::class => function (View $view) {
            $view->addLoader([UrlListener::class, 'resolveRelativeUrl']);

            $view->addFunction('trans', function ($id) {
                return __($id, 'yootheme');
            });

            $view->addFunction('formatBytes', function ($bytes, $precision = 0) {
                return size_format($bytes, $precision);
            });
        },

        Updater::class => function (Updater $updater) {
            $updater->add(Path::get('./updates.php'));
        },
    ],

    'services' => [
        BaseSystemCheck::class => SystemCheck::class,
    ],

    'loaders' => [
        'theme' => [ThemeLoader::class, 'load'],
    ],
];
