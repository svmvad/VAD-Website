<?php

namespace Netdust\VAD\Providers;

use Netdust\Service\Posts\Taxonomy;
use Netdust\Service\Posts\Post;
use Netdust\Service\Users\Role;

use Netdust\VAD\Hooks\VAD_ApplicationHooks;
use Netdust\VAD\Hooks\TemplateHooks;

class vad_Application extends \Netdust\Utils\ServiceProvider {

    /**
     * early registration, init providers and settings here
     */
    public function register() {

        $this->container->singleton(VAD_ApplicationHooks::class );
        $this->container->singleton(TemplateHooks::class );
    }

    /**
     * called right after 'after_setup_theme'
     * provider can start doing some work
     */
    public function boot() {

        $this->setup_hooks();
        $this->setup_blocks();
        $this->setup_user_roles();
        $this->setup_post_types();
        $this->setup_script_styles();
    }

    public function setup_hooks() {
        add_action(
            'init',
            $this->container->callback(VAD_ApplicationHooks::class, 'tags_support_all')
        );
        add_action(
            'pre_get_posts',
            $this->container->callback(VAD_ApplicationHooks::class, 'tags_support_query')
        );
        add_action(
            'admin_body_class',
            $this->container->callback(VAD_ApplicationHooks::class, 'add_roles_to_class')
        );
        add_action(
            'current_screen',
            $this->container->callback(VAD_ApplicationHooks::class, 'current_screen')
        );

        // add custom permalink to artikels
        add_filter(
            'post_link',
            $this->container->callback(VAD_ApplicationHooks::class, 'custom_artikels_permalink_post')
            , 10, 3 );
        add_filter(
            'term_link',
            $this->container->callback(VAD_ApplicationHooks::class, 'custom_artikels_permalink_archive')
            , 10, 3 );
        add_action(
            'generate_rewrite_rules',
            $this->container->callback(VAD_ApplicationHooks::class, 'custom_rewrite_rules')
        );
        add_action(
            'plugins_loaded',
            $this->container->callback(VAD_ApplicationHooks::class, 'load_textdomain')
        );

        add_filter( 'netdust_disable_cache', function( $bool ) {
            return true;
        } );

    }

    /**
     *
     * Wrapper method binding an id to implementation and calling builder methods
     */
    protected function bind( $key, $class, $args, $methods=[] ) {
        $this->container->when( $key )->needs('$args' )->give( $args );
        $this->container->singleton( $key, $class, $methods );
        $this->container->get( $key );
    }

    public function setup_blocks() {
        foreach( $this->app()->config('blocks') as $key => list( $class, $args ) ) {
            $this->bind( $key, $class, $args, ['do_actions']);
        }
    }

    public function setup_user_roles() {
        add_action( 'init', function() {
            global $wp_roles;
            if (!isset($wp_roles))
                $wp_roles = new \WP_Roles();

            foreach ($this->app()->config('roles') as $key => $args) {
                $wp_roles->remove_role($key);
                $this->bind( $key, Role::class, $args, ['do_actions']);
            }
        } );
    }
    public function setup_post_types() {

        foreach( $this->app()->config('posts') as $key => $inst ) {
            $this->bind( $key, Post::class, $inst, ['do_actions']);
        }

        foreach( $this->app()->config('taxonomies') as $key => $inst ) {
            $this->bind( $key, Taxonomy::class, $inst, ['do_actions']);
        }

        add_action( 'init', function() {
            unregister_taxonomy_for_object_type( 'category', 'post' );
        } );

    }

    public function setup_script_styles() {
        foreach( $this->app()->config('styles') as $key => $inst ) {
            $this->styles()->add(
                $key, $inst
            );
        }

        foreach( $this->app()->config('scripts') as $key => $inst ) {
            $this->scripts()->add(
                $key, $inst
            );
        }
    }
}