<?php

namespace Netdust\VAD\Hooks;


class VAD_ApplicationHooks
{

    /***********************************
    Modifying case study permalinks
     ************************************/
    /**
     * Modify Permalinks for the Case Studies Category
     *
     * @author Nikki Stokes
     * @link https://thebizpixie.com/
     *
     * @param string $permalink
     * @param WP_Post $post
     * @param array $leavename
     */
// Modify the individual case study post permalinks
    public function custom_artikels_permalink_post( $permalink, $post, $leavename ) {
        // Get the categories for the post
        if (  $post->post_type == "post" ) {
            $permalink = trailingslashit( home_url('/artikels/'. $post->post_name . '/' ) );
        }
        return $permalink;
    }


// Modify the "case studies" category archive permalink
    public function custom_artikels_permalink_archive( $permalink, $term, $taxonomy ){
        // Get the category ID
        $category = get_taxonomy( $term->taxonomy );
        // Check for desired category
        if( !empty( $category ) && in_array( 'post', $category->object_type ) ) {
            $permalink = trailingslashit( home_url('/artikels/'. $category->name . '/' . $term->slug .'/') );
        }

        return $permalink;
    }


// Add rewrite rules so that WordPress delivers the correct content
    public function custom_rewrite_rules( $wp_rewrite ) {
        // This rule will match the post name in /artikels/%postname%/ structure
        //$new_rules['^artikels/([^/]+)/([^/]+)/?$'] = 'index.php?$matches[1]=$matches[2]';
        $new_rules['^artikels/([^/]+)/?$'] = 'index.php?name=$matches[1]';
        $new_rules['^artikels/?$'] = 'index.php?page_id=1163';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;

        return $wp_rewrite;
    }

    public function add_roles_to_class( $classes ) {
        if( is_user_logged_in() ) {
             $user_role = wp_get_current_user()->roles;
             $classes .= ' user-role-' . implode("_",$user_role);
        }
        return $classes;
    }

    public function load_textdomain() {
        // Set filter for plugin language directory
        $lang_dir = app()->dir() . '/languages/';
        $lang_dir = apply_filters( app()->text_domain . '_languages_directory', $lang_dir );

        // Load plugin translation file
        load_plugin_textdomain( app()->text_domain, false, $lang_dir );
    }

    public function current_screen() {
        if ( false === strpos( get_current_screen()->id, app()->name ) ) {
            return;
        }

        // netdust pages footer credits.
        add_filter(
            'admin_footer_text',
            function() {
                return app()->config( app()->name, 'footer_text');
            }
        );
    }

    // add tag support to pages
    public function tags_support_all() {
        register_taxonomy_for_object_type('post_tag', 'page');
    }

// ensure all tags are included in queries
    public function tags_support_query($wp_query) {
        if ($wp_query->get('tag')) $wp_query->set('post_type', 'any');
    }

}