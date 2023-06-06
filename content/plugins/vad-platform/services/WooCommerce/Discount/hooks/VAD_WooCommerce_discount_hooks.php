<?php

namespace Netdust\VAD\Services\WooCommerce\Discount\hooks;

class VAD_WooCommerce_discount_hooks {

    protected $discounts;

    public function init() {
        if (!is_admin() && !$this->is_checkout())
            $this->discounts = array();
    }

    public function get_cart_item_price($price, $item, $key) {

        $product = $item['data'];

        $label = '';
        if( is_array($this->discounts) && !empty( $this->discounts[$product->get_id()] ) ) {
            $label = $this->discounts[$product->get_id()];
        }
        return  $price . ' ' . $label;
    }

    public function get_sale_price($sale_price, $product) {

        if (is_admin() && !is_ajax() /* || empty($sale_price) */)
            return $sale_price;

        $pid =  $product->get_id();

        if (empty($sale_price))
            $sale_price = $product->get_regular_price();


        //We check if there is a quantity pricing in order to apply that discount in the cart or checkout pages
        if (is_cart() || $this->is_checkout()) {
            $sale_price = $this->apply_quantity_based_price_if_needed($product, $sale_price);
        }

        return $sale_price;
    }

    public function get_regular_price($regular_price, $product) {

        if (is_admin() && !is_ajax())
            return $regular_price;

        $pid =  $product->get_id();

        //We check if there is a quantity pricing in order to apply that discount in the cart or checkout pages
        if (is_cart() || $this->is_checkout()) {
            $regular_price = $this->apply_quantity_based_price_if_needed($product, $regular_price);
        }

        return $regular_price;
    }

    public function get_variations_prices($prices, $product) {
        foreach ($prices["regular_price"] as $variation_id => $variation_price) {
            $variation = wc_get_product($variation_id);

            $variation_sale_price = $prices["sale_price"][$variation_id];
            $prices["sale_price"][$variation_id] = $this->get_sale_price($variation_sale_price, $variation);

            $variation_price = $prices["price"][$variation_id];
            $prices["price"][$variation_id] = $this->get_sale_price($variation_price, $variation);
        }

        return $prices;
    }

    private function is_checkout()
    {
        $is_checkout=false;
        if (!is_admin() && function_exists( 'is_checkout' ) && is_checkout())
            $is_checkout=true;

        return $is_checkout;
    }

    /**
     * Apply dynamic price based on product quantity rules.
     *
     * @return int discounted product price
     *
     */
    private function apply_quantity_based_price_if_needed($product, $normal_price) {

        //We check if there is a quantity based discount for this product
        $products_qties = $this->get_cart_item_quantities();
        $quantity_pricing = get_post_meta($product->get_id(), "o-discount", true);

        //What product?
        $id_to_check = $product->get_id();

        //Get rules, intervals as default
        $rules_type = $quantity_pricing["rules-type"] ?? "none";

        //Do we need to do anything?
        if( !isset( $quantity_pricing ) || isset( $quantity_pricing["type"] ) && $quantity_pricing["type"] == 'coupon' )
            return $normal_price;

        if (!isset($products_qties[$id_to_check]) || empty($quantity_pricing) || !isset($quantity_pricing["enable"]))
            return $normal_price;

        if (isset($quantity_pricing["rules"]) && $rules_type == "intervals" ) {
            // price bases on interval
            foreach ($quantity_pricing["rules"] as $rule)
            {
                if (
                    ($rule["min"] === "" && $products_qties[$id_to_check] <= $rule["max"])
                    || ($rule["min"] === "" && $rule["max"] === "")
                    || ($rule["min"] <= $products_qties[$id_to_check] && $rule["max"] === "")
                    || ($rule["min"] <= $products_qties[$id_to_check] && $products_qties[$id_to_check] <= $rule["max"])
                ) {
                    if ($quantity_pricing["type"] == "fixed") {
                        $normal_price -= $rule["discount"];
                        $this->discounts[$id_to_check] = '( -'.$rule["discount"].'€ )';
                    }
                    else if ($quantity_pricing["type"] == "percentage")
                        $normal_price -= ($normal_price * $rule["discount"]) / 100;
                        $this->discounts[$id_to_check] = '( -'.$rule["discount"].'% )';
                    break;
                }
            }
        }
        else if (isset($quantity_pricing["rules-by-step"]) && $rules_type == "steps") {
            // price based on steps
            foreach ($quantity_pricing["rules-by-step"] as $rule) {
                if ($products_qties[$id_to_check] % $rule["every"] == 0) {
                    if ($quantity_pricing["type"] == "fixed")
                        $normal_price-=$rule["discount"];
                    else if ($quantity_pricing["type"] == "percentage")
                        $normal_price-=($normal_price * $rule["discount"]) / 100;
                    break;
                }
            }
        }

        return $normal_price;
    }
    /**
     * Get the amount if items in basket / product.
     *
     * @return array array with products order amount
     *
     * @since     0.0.1
     * @version   0.0.1
     */
    private function get_cart_item_quantities() {
        global $woocommerce;
        $item_qties = array();
        foreach ($woocommerce->cart->cart_contents as $cart_item) {
            if (!empty($cart_item["variation_id"]))
                $item_qties[$cart_item["variation_id"]] = $cart_item["quantity"];
            else
                $item_qties[$cart_item["product_id"]] = $cart_item["quantity"];
        }
        return $item_qties;
    }


}