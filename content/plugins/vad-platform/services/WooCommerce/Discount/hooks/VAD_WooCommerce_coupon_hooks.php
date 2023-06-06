<?php

namespace Netdust\VAD\Services\WooCommerce\Discount\hooks;

class VAD_WooCommerce_coupon_hooks
{

    public function enable_shipping_calc_on_cart( $show_shipping ) {

       // ntdst_error_log( WC()->cart->applied_coupons );
        if( count( WC()->cart->applied_coupons ) > 0 && WC()->cart->get_coupon_discount_amount( $this->get_discount_code() ) > 0 ) {
            return false;
        }
        return true;
    }


    public function get_cart_item_price_label($item, $key) {

        $label = '';
        if( $this->is_discountable( $item ) ) {
            $label = '<br>( gratis examplaren toegekend als korting )';
        }
        echo  $label;
    }

    /**
     * Processes the request to check if there should be a coupon coupon
     * applied to this basket.
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    function woocommerce_after_calculate_totals() {
        // We don't want this function to recurse when running
        // so we remove it from the hook, and then add it again after
        remove_action( 'woocommerce_after_calculate_totals', array( $this, 'woocommerce_after_calculate_totals' ) );

        $this->apply_coupon_test();

        add_action( 'woocommerce_after_calculate_totals', array( $this, 'woocommerce_after_calculate_totals' ) );
    }

    /**
     * Checks if the coupon offer is currently active
     * @return bool true/false as per admin settings
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    function is_coupon_active() {

        return true;

        /*
        $this->options = get_option( 'wc_coupon_options' );

        if( isset( $this->options['coupon_active'] ) ) {
            if( $this->options['coupon_active'] == 1) {
                return true;
            }
        }

        return false;
        */
    }

    /**
     * Returns the coupon code
     * @return string coupon code from admin settings
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    function get_discount_code( ) {

        return 'free_items_coupon';
        /*
        $this->options = get_option( 'wc_coupon_options' );

        return $this->options['discount_code'];
        */
    }


    /**
     * Checks if the given item is discountable. No real implementation yet
     *
     * @param  array      $item       The product to check.
     * @return boolean                If the item is discountable or not
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    function is_discountable( $item ) {

        $pid = $item['data']->get_id();
        $quantity_pricing = get_post_meta($pid, "o-discount", true);

        if( isset($quantity_pricing["enable"]) && $quantity_pricing["type"] === "coupon" ) {
            return true;
        }

        return false;

    }


    /**
     * Check if a give code is a valid coupon code
     * This is currently done by checking if the string
     * given is the same as the one in the settings. This isn't
     * totally ideal, but currently banking on there being no
     * active codes that happen to be named the exact same thing
     * as the virtual code we're using here. There is scope
     * for expanding this so that it checks on a more advanced set
     * of variables, e.g. we could store a variable somewhere against
     * the basket to say if its had a virtual coupon applied to it or not.
     *
     * @param  string    $code    The code to check against
     * @return boolean            If code matches or not
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    function check_coupon_code( $code ) {

        $discount_code = strtolower( trim( $this->get_discount_code() ) );
        $code = strtolower( trim( $code ) );

        if( $discount_code == $code ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Apply the coupon tests and then apply the appropriate
     * coupon discount if it's required.
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    function apply_coupon_test() {

        $discount = $this->get_discount();

        // If no discount should be applied then
        // we need to remove any existing coupons that
        // have been applied by the system, incase
        // someone has added an item and then removed it etc.
        if ( empty( $discount ) ) {

            foreach ( WC()->cart->applied_coupons as $coupon_code ) {
                if ( $this->check_coupon_code( $coupon_code ) ) {
                    WC()->cart->remove_coupon( $coupon_code );
                }
            }

        } else {

            // Check the existing codes and if we already have
            // one of our coupon codes applied then just use that.
            foreach ( WC()->cart->applied_coupons as $coupon_code ) {
                if ( $this->check_coupon_code( $coupon_code ) ) {
                    $code = $coupon_code;
                    break;
                }
            }

            // If there's no code yet then we'll make a new one and apply
            // it to the basket.
            if ( ! isset( $code ) ) {
                if ( $this->is_coupon_active() ) {
                    $bogo_coupon_code = $this->get_discount_code();
                    WC()->cart->add_discount( $bogo_coupon_code );
                    WC()->session->set( 'refresh_totals', true );
                }
            }
        }
    }

    /**
     * Calculate the valid discount amount to apply to a basket.
     *
     * @return int amount to discount
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    function get_discount() {

        $discount = 0;

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

            // Note that we are doing this on a per quantity basis
            if( $this->is_discountable($cart_item) ) {

                $discount += $this->get_item_discount ( $cart_item );

            }
        }

        return $discount;

    }

    /**
     * Calculate the valid discount amount to apply to an item.
     *
     * @return int amount to discount
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    function get_item_discount( $cart_item ) {
        $pid = $cart_item['data']->get_id();
        $quantity_pricing = get_post_meta($pid, "o-discount", true);

        if ( isset($quantity_pricing["coupon"]) ) {
            foreach ($quantity_pricing["coupon"] as $rule) {
                $rule['min'] = $rule['min'] ?? 0; // default to 0, discount starts at min
                if ( $rule['min'] <= $cart_item['quantity'] ) {
                    $discount = $rule['discount'];
                }
            }
        }

        if( $discount >  $cart_item['data']->get_price() * $cart_item['quantity'] ) {
            $discount =  $cart_item['data']->get_price() * $cart_item['quantity'];
        }

        return $discount;
    }

    /**
     * Applies the virtual coupon to the basket.
     *
     * @param  array     $data    Coupon data to apply
     * @param  string    $code    The code to apply
     *                            This will currently match $this->get_discount_code()
     *                            but may change in future.
     *
     * @return array      $data   Coupon data
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    function woocommerce_get_shop_coupon_data( $data, $code ) {

        if ( empty( $code ) || empty( WC()->cart ) ) {
            return $data;
        }

        $discount = $this->get_discount();

        // Fallback checks after fallback checks... If the coupon offer isnt
        // active or the code doesn't look right then don't apply the code.
        if ( $this->is_coupon_active() && $this->check_coupon_code( $code ) ) {
            $data = array(
                'id' => -1,
                'code' => $this->get_discount_code(),
                'description' => 'Gratis exemplaren',
                'amount' => $discount,
                'coupon_amount' => $discount
            );
        }

        return $data;
    }

    /**
     * Update the label for the promotion if its a coupon promotion to
     * make it clear that the coupon was applied automatically. This
     * can prevent any confusion to the customer, hopefully.
     *
     * @param  string     $label      Label to show next to item in basket
     * @param  object     $coupon     The coupon that was applied.
     * @return string     $label      The updated label, if required.
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    function woocommerce_cart_totals_coupon_label( $label, $coupon ) {

        // If the code matches up to what we expect a coupon code to look
        // like then we update the label to read automatic promotion
        // instead of the default 'Coupon:'
        if ( $this->check_coupon_code( $coupon->get_code() ) ) {
            $label = 'Toegekende korting:';
        }

        return $label;
    }
    
}