<?php

namespace Netdust\VAD\Shortcodes;

use Netdust\Service\Shortcodes\Shortcode;
use Netdust\Traits\Templates;

class Simple_shortcode extends Shortcode
{

    use Templates;

    protected function shortcode_actions( array $atts ) {
        return $this->get_template($this->shortcode, $atts);
    }

}