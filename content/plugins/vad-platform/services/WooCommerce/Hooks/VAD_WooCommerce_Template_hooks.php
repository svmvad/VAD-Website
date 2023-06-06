<?php

namespace  Netdust\VAD\Services\WooCommerce\Hooks;

class VAD_WooCommerce_Template_hooks
{
    public function setup_nav() {

        bp_core_new_nav_item( array(
            'name'            => 'Mijn bestellingen',
            'slug'            => 'bestellingen',
            'screen_function' => array( $this, 'woocommerce_bestellingen_page' ),
            'position'        => 75,
            'user_has_access' => bp_core_can_edit_settings()
        ) );

    }


    public function custom_breadcrumb( $crumbs, $breadcrumb ) {
        if ( ! is_product() ) {
            return $crumbs;
        } else {

            unset($crumbs[1]); // this isn't enough, it would leave a trailing delimiter
            $newBreadC = array_values($crumbs); //therefore create new array

            return $newBreadC; //return the new array
        }
    }

    public function get_nav_link( $slug, $parent_slug = '' ) {
        $displayed_user_id = bp_displayed_user_id();
        $user_domain       = ( ! empty( $displayed_user_id ) ) ? bp_displayed_user_domain() : bp_loggedin_user_domain();
        if ( ! empty( $parent_slug ) ) {
            $nav_link = trailingslashit( $user_domain . $parent_slug . '/' . $slug );
        } else {
            $nav_link = trailingslashit( $user_domain . $slug );
        }

        return $nav_link;
    }

    public function woocommerce_bestellingen_page( ){
        add_action( 'bp_template_content', array( $this, 'woocommerce_bestellingen_page_content' ) );
        bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
    }

    public function woocommerce_bestellingen_page_content() {
        do_action( 'template_notices' );
        do_action( 'bp_learndash_before_billing_page_content' );
        ?>
        <style>
            .wpi-pending{ display: none !important; }
            .bsui .btn.pay{ display: none !important; }
            .bsui .btn.delete{ display: none !important; }
        </style>
        <div class="bp-profile-wrapper need-separator">

            <div class="bp-profile-content">


                <div class="profile public">


                    <div class="group-separator-block">
                        <header class="entry-header profile-loop-header profile-header flex align-items-center">
                            <h1 class="entry-title bb-profile-title">Overzicht</h1>
                        </header>
                        <div class="bp-widget overzicht">
                            <table class="profile-fields bp-tables-user">


                                <tbody><tr class="field_1 field_first-name field_order_0 required-field visibility-public field_type_textbox">

                                    <td class="label">VADC-00797</td>

                                    <td class="data"><p>Factsheet alcohol</p>
                                    </td>

                                </tr>


                                <tr class="field_3 field_nickname field_order_0 required-field visibility-adminsonly alt field_type_textbox">

                                    <td class="label">VADC-00798</td>

                                    <td class="data"><p>Factsheet XTC</p>
                                    </td>

                                </tr>


                                </tbody></table>
                        </div>
                    </div>

                </div><!-- .profile -->


            </div>

        </div>
        <?php

    }
}