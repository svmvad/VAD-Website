<?php

namespace Netdust\VAD\Modules\BuddyBoss\shortcodes;


class BuddyBossUserMenu_shortcode extends \Netdust\Loaders\Shortcodes\Factories\Shortcode_Instance
{

    public function shortcode_actions( $atts ) {

        $this->enqueue_scripts();

        ob_start();
        include( get_template_directory() . '/template-parts/header-aside.php' );
        return ob_get_clean();
    }

    protected function enqueue_scripts(){

       wp_enqueue_script( 'layout-js',app()->url() . '/modules/BuddyBoss/assets/layout.js', array( 'jquery' ), app()->version(), true );
       wp_enqueue_style( 'layout-css',app()->url() . '/modules/BuddyBoss/assets/layout.css', null, app()->version() );


    }

}