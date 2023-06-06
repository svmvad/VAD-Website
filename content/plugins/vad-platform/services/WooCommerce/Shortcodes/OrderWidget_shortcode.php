<?php

namespace Netdust\VAD\Services\WooCommerce\Shortcodes;

use Netdust\Service\Shortcodes\Shortcode;
use Netdust\Traits\Templates;

class OrderWidget_shortcode extends Shortcode
{

    use Templates;

    public function shortcode_actions(  array $atts ) {

        //print to screen
        return $this->get_template('product-order', []);

    }

    /**
     * @inheritDoc
     */
    protected function get_template_root_path() {

        return dirname(__DIR__, 1) . '/templates';

    }

    /**
     * Fetches the template group name.
     *
     * @since 1.0.0
     *
     * @return string The template group name
     */
    protected function get_template_group() {
        return 'product';
    }


}