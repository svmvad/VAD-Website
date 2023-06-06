<?php

namespace Netdust\VAD\Services\WooCommerce\Discount\hooks;

class VAD_WooCommerce_metabox_hooks {
    /**
     * Saves the discount data
     * @param int $post_id
     */
    public function save_discount($post_id) {
        $meta_key = "o-discount";
        if (isset($_POST[$meta_key])) {
            update_post_meta($post_id, $meta_key, $_POST[$meta_key]);
            app()->remove_transients();
        }
    }
}