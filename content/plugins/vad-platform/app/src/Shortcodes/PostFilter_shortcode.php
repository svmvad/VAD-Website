<?php

namespace Netdust\VAD\Shortcodes;


class PostFilter_shortcode extends \Netdust\Service\Shortcodes\Shortcode
{
    /**
     * returns output from post-filter block
     * @param array $atts
     * @return mixed
     */
    protected function shortcode_actions( array $atts ) {
        return app()->get( 'post-filter' )->as_shortcode($atts);
    }
}