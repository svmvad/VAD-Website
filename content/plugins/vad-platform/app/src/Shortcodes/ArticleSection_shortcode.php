<?php

namespace Netdust\VAD\Shortcodes;
use Netdust\Service\Shortcodes\Shortcode;

class ArticleSection_shortcode extends Shortcode
{

    /**
     * returns output from articles-section block
     * @param array $atts
     * @return mixed
     */
    protected function shortcode_actions( array $atts ) {
        return app()->get( 'related-articles-block' )->as_shortcode($atts);
    }

}