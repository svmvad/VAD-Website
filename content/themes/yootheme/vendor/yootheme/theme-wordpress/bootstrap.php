<?php

namespace YOOtheme\Theme\Wordpress;

use YOOtheme\Config;
use YOOtheme\Theme\SystemCheck as BaseSystemCheck;
use YOOtheme\Theme\Updater;
use YOOtheme\View;

require __DIR__ . '/supports.php';
require __DIR__ . '/functions.php';

return [
    'config' => [
        'app' => ['isCustomizer' => is_customize_preview()],
    ],

    'theme' => function (Config $config) {
        return $config->loadFile(__DIR__ . '/config/theme.json');
    },

    'events' => [
        'app.request' => [Listener\CheckUserCapability::class => 'handle'],
        'url.resolve' => [Listener\AddCustomizeParameter::class => '@handle'],
    ],

    'actions' => [
        'admin_menu' => [Listener\AddAdminMenuButton::class => '@handle'],
        'after_setup_theme' => [ThemeLoader::class => 'setupTheme'],
        'after_switch_theme' => [Listener\CopyThemeConfig::class => '@handle'],
        'comment_form_after' => [Listener\FilterCommentHtml::class => 'form'],
        'customize_controls_init' => [Listener\LoadCustomizer::class => '@handle'],
        'customize_register' => [Listener\LoadThemeConfig::class => '@handle'],
        'get_header' => [Listener\LoadThemeHead::class => '@handle'],
        'init' => [Listener\LoadChildTheme::class => '@handle'],
        'template_include' => [Listener\AddPageLayout::class => '@handle'],
        'wp_head' => [Listener\LoadCustomScript::class => ['@handle', 20]],
        'wp_prepare_themes_for_js' => [Listener\DisableAutoUpdate::class => 'handle'],

        'wp_loaded' => [
            ThemeLoader::class => 'initTheme',
            Listener\LoadThemeUpdate::class => '@handle',
        ],

        'wp_enqueue_scripts' => [
            Listener\LoadjQueryScript::class => '@handle',
            Listener\FilterCommentHtml::class => 'script',
        ],
    ],

    'filters' => [
        'cancel_comment_reply_link' => [Listener\FilterCommentHtml::class => 'cancelReplyLink'],
        'comment_reply_link' => [Listener\FilterCommentHtml::class => 'replyLink'],
        'get_comment_author_link' => [Listener\FilterCommentHtml::class => 'authorLink'],
        'get_site_icon_url' => [Listener\FilterIconUrl::class => '@handle'],
        'post_gallery' => [Listener\FilterPostGallery::class => ['handle', 10, 3]],
        'site_icon_meta_tags' => [Listener\FilterIconMetaTags::class => '@handle'],
        'upload_mimes' => [Listener\AddSvgMimeType::class => 'handle'],
        'wp_check_filetype_and_ext' => [Listener\AddSvgFileType::class => ['handle', 10, 4]],
    ],

    'extend' => [
        View::class => function (View $view) {
            $view->addLoader([UrlLoader::class, 'resolveRelativeUrl']);
            $view->addFunction('trans', fn($id) => __($id, 'yootheme'));
            $view->addFunction(
                'formatBytes',
                fn($bytes, $precision = 0) => size_format($bytes, $precision)
            );
        },

        Updater::class => function (Updater $updater) {
            $updater->add(__DIR__ . '/updates.php');
        },
    ],

    'services' => [
        BaseSystemCheck::class => SystemCheck::class,
        Listener\AddCustomizeParameter::class => '',
    ],

    'loaders' => [
        'theme' => [ThemeLoader::class, 'load'],
    ],
];
