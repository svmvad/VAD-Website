<?php

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
add_action('admin_enqueue_scripts', 'enqueue_admin_styles');
//add_filter('use_block_editor_for_post','__return_false');

function enqueue_admin_styles() {
  wp_enqueue_style('admin-styles', get_stylesheet_directory_uri().'/css/ntdst/admin.css');
}
function enqueue_parent_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}