<?php

namespace Netdust\VAD\Services\WooCommerce\Discount\hooks;

class VAD_WooCommerce_template_hooks {
    /**
     * Adds new tabs in the product page
     */
    function get_product_tab_label() {

        ?>
        <li class="vad_woocommerce_quantity_pricing"><a href="#vad_woocommerce_quantity_pricing_data"><span><?php _e('Quantity Based Pricing', 'vad_woocommerce'); ?></span></a></li>
        <?php

    }

    public function get_product_tab_data() {
error_log("dit is sdlfjdsfjdsdslkfdlj");
//        var_dump("yes");
        $begin = array(
            'type' => 'sectionbegin',
            'id' => 'wad-quantity-pricing-rules',
        );

        $discount_enabled = array(
            'title' => __('Enabled', 'wad'),
            'name' => 'o-discount[enable]',
            'type' => 'checkbox',
            'default' => 1,
            'desc' => __('Enable/Disable this feature', 'wad'),
        );

        $discount_type = array(
            'title' => __('Discount type', 'wad'),
            'name' => 'o-discount[type]',
            'type' => 'radio',
            'options' => array(
                "coupon" => __("Coupon on product total", "wad"),
                "percentage" => __("Percentage on product price", "wad"),
                "fixed" => __("Fixed amount on product price", "wad"),
            ),
            'default' => 'coupon',
            'desc' => __('Apply a coupon, percentage or a fixed amount discount', 'wad'),
        );

        $rules_types = array(
            'title' => __('Rules type', 'wad'),
            'name' => 'o-discount[rules-type]',
            'type' => 'radio',
            'options' => array(
                "intervals" => __("Intervals", "wad"),
                "steps" => __("Steps", "wad"),
            ),
            'default' => 'intervals',
            'desc' => __('If Intervals, the intervals rules will be used.<br>If Steps, the steps rules will be used.', 'wad'),
        );

        $min = array(
            'title' => __('Min', 'wad'),
            'name' => 'min',
            'type' => 'number',
            'default' => '',
        );

        $max = array(
            'title' => __('Max', 'wad'),
            'name' => 'max',
            'type' => 'number',
            'default' => '',
        );

        $every = array(
            'title' => __('Every X items', 'wad'),
            'name' => 'every',
            'type' => 'number',
            'default' => '',
        );

        $discount = array(
            'title' => __('Discount', 'wad'),
            'name' => 'discount',
            'type' => 'number',
            'custom_attributes' => array("step" => "any"),
            'default' => '',
        );

        $coupon_rules = array(
            'title' => __('Coupon rules', 'wad'),
            'desc' => __('If quantity ordered is Min, then a discount will be substracted as coupon from the total products price.', 'wad'),
            'name' => 'o-discount[coupon]',
            'type' => 'repeatable-fields',
            'id' => 'coupon_rules',
            'fields' => array($min, $discount),
        );

        $discount_rules = array(
            'title' => __('Intervals rules', 'wad'),
            'desc' => __('If quantity ordered between Min and Max, then the discount specified will be applied. <br>Leave Min or Max empty for any value (joker).', 'wad'),
            'name' => 'o-discount[rules]',
            'type' => 'repeatable-fields',
            'id' => 'intervals_rules',
            'fields' => array($min, $max, $discount),
        );


        $discount_rules_steps = array(
            'title' => __('Steps Rules', 'wad'),
            'desc' => __('If quantity ordered is a multiple of the step, then the discount specified will be applied.', 'wad'),
            'name' => 'o-discount[rules-by-step]',
            'type' => 'repeatable-fields',
            'id' => 'steps_rules',
            'fields' => array($every, $discount),
        );

        $end = array('type' => 'sectionend');
        $settings = array(
            $begin,
            $discount_enabled,
            $discount_type,
            $rules_types,
            $coupon_rules,
            $discount_rules,
            $discount_rules_steps,
            $end
        );
        ?>
        <div id="vad_woocommerce_quantity_pricing_data" class="panel woocommerce_options_panel wpc-sh-triggerable">
            <div class="options_group">
                <?php
                echo o_admin_fields($settings);
                ?>
            </div>
        </div>
        <?php
        global $o_row_templates;
        ?>
        <script>
            var o_rows_tpl =<?php echo json_encode($o_row_templates); ?>;
        </script>
        <?php
    }


}