<?php

namespace Netdust\VAD\Services\WooCommerce\Discount;

use Netdust\Utils\ServiceProvider;
use Netdust\VAD\Services\WooCommerce\Discount\hooks\VAD_WooCommerce_metabox_hooks;
use Netdust\VAD\Services\WooCommerce\Discount\hooks\VAD_WooCommerce_template_hooks;
use Netdust\VAD\Services\WooCommerce\Discount\hooks\VAD_WooCommerce_discount_hooks;
use Netdust\VAD\Services\WooCommerce\Discount\hooks\VAD_WooCommerce_coupon_hooks;

class VAD_WooCommerce_Discount extends ServiceProvider
{
    public function register() {
        error_log("dit is sdlfjdsfjdsdslkfdlj");
        $this->enqueue_scripts();
        $this->add_hooks();
        $this->require();
    }

    public function enqueue_scripts() {
        add_action( 'admin_enqueue_scripts', function() {
            wp_register_script('o-admin', app()->url() . '/services/WooCommerce/Discount/assets/js/o-admin.js', null, app()->version(), true);
            wp_enqueue_script('o-admin');

            wp_register_script('wad-admin', app()->url() . '/services/WooCommerce/Discount/assets/js/wad-admin.js', null, app()->version(), true);
            wp_enqueue_script('wad-admin');

            wp_register_style('wad-admin', app()->url() . '/services/WooCommerce/Discount/assets/css/wad-admin.css', null, app()->version());
            wp_enqueue_style('wad-admin');

            wp_register_style('flexiblegs', app()->url() . '/services/WooCommerce/Discount/assets/css/flexiblegs.css', null, app()->version());
            wp_enqueue_style('flexiblegs');

            wp_register_style('ui', app()->url() . '/services/WooCommerce/Discount/assets/css/UI.css', null, app()->version());
            wp_enqueue_style('ui');
        }, 1 );
    }

    public function add_hooks()
    {
        $metabox_hooks = ( new VAD_WooCommerce_metabox_hooks() );
        add_action( 'save_post_o-discount', [$metabox_hooks, 'save_discount'] );
        add_action( 'save_post_product', [$metabox_hooks, 'save_discount']  );

        $template_hooks = ( new VAD_WooCommerce_template_hooks() );
        add_filter( 'woocommerce_product_write_panel_tabs', [$template_hooks, 'get_product_tab_label']);
        add_action( 'woocommerce_product_data_panels', [$template_hooks, 'get_product_tab_data']);

        // quantity based
        $discount_hooks = ( new VAD_WooCommerce_discount_hooks() );
        add_filter( 'woocommerce_init', [$discount_hooks, 'init'], 99 );
        add_filter( 'woocommerce_cart_item_price', [$discount_hooks, 'get_cart_item_price'], 99, 3 );
        add_filter( 'woocommerce_product_get_sale_price', [$discount_hooks, 'get_sale_price'], 99, 2 );
        add_filter( 'woocommerce_product_get_price', [$discount_hooks, 'get_regular_price'], 99, 2 );

        add_filter( 'woocommerce_variation_prices_sale_price', [$discount_hooks, 'get_sale_price'], 99, 2 );
        add_filter( 'woocommerce_variation_prices', [$discount_hooks, 'get_variations_prices'], 99, 2 );

        //coupon
        $coupon_hooks = ( new VAD_WooCommerce_coupon_hooks() );
        add_filter( 'woocommerce_cart_ready_to_calc_shipping', array( $coupon_hooks, 'enable_shipping_calc_on_cart'), 99, 1 );
        add_action( 'woocommerce_after_cart_item_name',   array( $coupon_hooks, 'get_cart_item_price_label' ), 99, 2 );
        add_action( 'woocommerce_after_calculate_totals',   array( $coupon_hooks, 'woocommerce_after_calculate_totals' ), 10, 2 );
        //add_action( 'woocommerce_add_to_cart',              array( $coupon_hooks, 'apply_coupon_test' ) );
        //add_action( 'woocommerce_check_cart_items',         array( $coupon_hooks, 'apply_coupon_test' ) );
        add_filter( 'woocommerce_get_shop_coupon_data',     array( $coupon_hooks, 'woocommerce_get_shop_coupon_data' ), 10, 2 );
        add_filter( 'woocommerce_cart_totals_coupon_label', array( $coupon_hooks, 'woocommerce_cart_totals_coupon_label' ), 10, 2 );

    }

    public function require() {
        require app()->dir() . '/services/WooCommerce/Discount/includes/utils.php';
    }

}