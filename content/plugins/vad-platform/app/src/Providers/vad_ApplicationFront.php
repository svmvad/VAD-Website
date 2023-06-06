<?php

namespace Netdust\VAD\Providers;

use Netdust\VAD\Hooks\TemplateHooks;

class vad_ApplicationFront extends \Netdust\Utils\ServiceProvider {

    /**
     * early registration, init providers and settings here
     */
    public function register() {
        $this->container->singleton(TemplateHooks::class );
    }

    /**
     * called right after 'after_setup_theme'
     * provider can start doing some work
     */
    public function boot() {
        $this->setup();
        $this->setup_hooks();
        $this->setup_shortcodes();
    }

    protected function setup() {

    }
    protected function setup_hooks() {

        add_filter(
            'the_content',
            $this->container->callback(TemplateHooks::class, 'merge_tags')
        );
        add_filter(
            'single_template',
            $this->container->callback(TemplateHooks::class, 'custom_single_template')
        );

    }

    public function setup_shortcodes() {
        foreach( $this->app()->config('shortcodes') as $key => list( $class, $args ) ) {
            $this->container->when( $key )->needs('$shortcode' )->give( $key );
            $this->container->when( $key )->needs('$args' )->give( $args );
            $this->container->bind( $key, $class, ['do_actions'] );
            $this->container->get( $key );
        }
    }

}