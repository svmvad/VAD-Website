<?php

namespace Netdust\VAD\Hooks;

class TemplateHooks {
    protected function get_merge_tags( ) {
        return apply_filters( 'merge_tags', array(
            '{post_id}' => get_queried_object_id(),
        ));
    }

    protected function add_merge_tags( $text, $merge_tags = array() ) {
        foreach ( $merge_tags as $key => $value ) {
            $text = str_replace( $key, $value, $text );
        }
        return wptexturize( $text );
    }

    public function merge_tags(  $text ) {
        return $this->add_merge_tags( $text, $this->get_merge_tags( ) );
    }

    public function custom_single_template($single) {

        global $post;

        /* Checks for single template by post type */
        if ( $post->post_type == 'post' ) {
            $template = app()->dir() . '/app/templates/front/post/single-'. (get_post_format() ?: 'standard') .'.php';
            if ( file_exists( $template  ) ) {
                return $template;
            }
        }

        return $single;

    }

    public function remove_mce_button( $buttons ) {
        array_unshift( $buttons, 'styleselect' );
        return $buttons;
    }

    public function add_tiny_mce_style_format( $init_array ) {
        $style_formats = array(
            // These are the custom styles
            array(
                'title' => 'Button',
                'block' => 'a',
                'classes' => 'uk-button uk-button-link',
                'attributes' => [ 'uk-icon'=>'arrow-right' ],
                'wrapper' => true,
            ),
            array(
                'title' => 'Content Block',
                'block' => 'div',
                'classes' => 'uk-card uk-card-body vad-content-block',
                'wrapper' => true,
            )
        );
        // Insert the array, JSON ENCODED, into 'style_formats'
        $init_array['style_formats'] = json_encode( $style_formats );

        return $init_array;
    }
}