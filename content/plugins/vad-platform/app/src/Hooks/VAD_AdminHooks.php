<?php

namespace Netdust\VAD\Hooks;


class VAD_AdminHooks
{

    // Function to change "posts" to "news" in the admin side menu
    public function move_post_menu_label() {

        // Remove existing parent menu.
        remove_menu_page( 'edit.php' );

        // add to custom
        add_submenu_page(
            'vad-website',
            'Artikels',
            'Artikels',
            'edit_posts',
            'edit.php',
        );

    }

// Function to change post object labels to "news"
    public function change_post_object_label() {
        global $wp_post_types;
        $labels = &$wp_post_types['post']->labels;
        $labels->name = 'Articles';
        $labels->singular_name = 'Article';
        $labels->add_new = 'Add Article';
        $labels->add_new_item = 'Add Article';
        $labels->edit_item = 'Edit Article';
        $labels->new_item = 'Article';
        $labels->view_item = 'View Article';
        $labels->search_items = 'Search Articles';
        $labels->not_found = 'No Articles found';
        $labels->not_found_in_trash = 'No Articles found in Trash';
    }


    public function remove_the_title(){
        $screen = get_current_screen();

        if ( isset( $screen ) && $screen->id == 'post' && is_admin()  )
            remove_post_type_support('post', 'title');
    }

    public function change_the_title( $title ){
        if( function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ( isset( $screen ) && $screen->id == 'edit-post' ){
                return ntdst_get_block_data( get_post( )->post_content, 'acf/block-panel', 'panel_title');
            }
        }

        return $title;
    }

    public function change_the_slug( $post_id, $post, $update ) {

        remove_action( 'save_post', [$this, 'change_the_slug'], 99 );

        if ( $post->post_type == 'post' ) {
            $title = ntdst_get_block_data( $post->post_content, 'acf/block-panel', 'panel_title');
            error_log( $title );
            wp_update_post( array(
                'ID'         => $post_id,
                'post_title' => $title,
                'post_name' => sanitize_title($title)
            ));
        }
    }
}