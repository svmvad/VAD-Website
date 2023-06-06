<?php

namespace Netdust\VAD\Services\WooCommerce\Shortcodes;

use Netdust\Service\Shortcodes\Shortcode;
use Netdust\Traits\Templates;

class CrossSellOverview_shortcode extends Shortcode
{

    public function shortcode_actions( $atts ) {

        $crosssells = get_post_meta( get_the_ID(), '_crosssell_ids',true);

        if(empty($crosssells)){
            return '';
        }

        return $this->get_template($this->shortcode, [
            "products" => $crosssells
        ]);

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


    /**
     * @inheritDoc
     */
    protected function get_template_root_path() {

        return dirname(__DIR__, 1) . '/templates';

    }


}