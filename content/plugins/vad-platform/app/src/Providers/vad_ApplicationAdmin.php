<?php

namespace Netdust\VAD\Providers;

use Netdust\Service\Pages\Admin\AdminPage;
use Netdust\VAD\Hooks\TemplateHooks;
use Netdust\VAD\Hooks\VAD_AdminHooks;

class vad_ApplicationAdmin extends  \Netdust\Utils\ServiceProvider {

    /**
     * early registration, init providers and settings here
     */
    public function register() {
        $this->container->singleton(VAD_AdminHooks::class );
        $this->container->singleton(TemplateHooks::class );
    }

    /**
     * called right after 'after_setup_theme'
     * provider can start doing some work
     */
    public function boot() {
        $this->setup_hooks();
        $this->setup_adminpages();
    }

    protected function setup_hooks() {

        /**
         * customize post menu item to artikel and move
         */
        add_action(
            'init',
            $this->container->callback(VAD_AdminHooks::class, 'change_post_object_label')
        );
        add_action(
            'admin_menu',
            $this->container->callback(VAD_AdminHooks::class, 'move_post_menu_label')
        );


        /**
         * changing tiny_mce options and adding styling
         */
        add_filter(
            'mce_buttons_2',
            $this->container->callback(TemplateHooks::class, 'remove_mce_button')
        );
        add_filter(
            'tiny_mce_before_init',
            $this->container->callback(TemplateHooks::class, 'add_tiny_mce_style_format')
        );

    }

    protected function setup_adminpages() {

        $this->container->get(AdminPage::class)->add(
            'admin-page', [
            'page_title' => 'VAD Website',
            'menu_title' => 'VAD Website',
            'capability' => 'read',
            'menu_slug' => 'vad-website',
            'icon' => app()->css_url() . '/img/vad.png',
            'position' => 4,
            'template_root' => dirname( __DIR__, 2 ) . '/templates'
        ]);

    }
}