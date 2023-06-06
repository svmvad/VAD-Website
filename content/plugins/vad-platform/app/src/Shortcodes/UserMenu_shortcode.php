<?php

namespace Netdust\VAD\Shortcodes;

use Netdust\Service\Shortcodes\Shortcode;
use Netdust\Traits\Templates;

class UserMenu_shortcode extends Shortcode
{

    protected function shortcode_actions( array $atts ) {
        return $this->get_template($this->shortcode, [
            'logged_in'=>is_user_logged_in(),
            'menu'=>$atts['menu']
        ]);
    }

    protected function get_template_group() {
        return parent::get_template_group() . '/usermenu';
    }

}