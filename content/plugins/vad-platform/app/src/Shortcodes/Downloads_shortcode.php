<?php

namespace Netdust\VAD\Shortcodes;

use Netdust\Service\Shortcodes\Shortcode;
use Netdust\Traits\Templates;

class Downloads_shortcode  extends Shortcode
{

    use Templates;

    public function shortcode_actions( $atts ) {
        return app()->get( 'downloads-block' )->as_shortcode($atts);
    }

}