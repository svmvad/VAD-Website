<?php
/**
 * Admin Section Template
 *
 * @author: Alex Standiford
 * @date  : 12/21/19
 */

use Netdust\Loaders\Admin\Abstracts\AdminSection;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) || ! $template instanceof AdminSection ) {
    return;
}
?>


<div >

    <div class="ld-bootview">

        <div class="ld-overview">
            <div class="ld-overview--columns" >
                <div class="ld-overview--column ld-overview--widget table">
                    <h3>WooCommerce Settings</h3>
                    <div>
                        <form method="post" id="runner-dispatch">
                            <h2><?= $template->get_param( 'title', '' ) ?></h2>
                            <p style="max-width:700px;"><?= $template->get_param( 'description', '' ) ?></p>
                            <table class="form-table">
                                <tbody>

                                <?php

                                foreach ( $template->fields as $key => $field ) {
                                    $field = $template->get_field( $key );

                                    if ( $field instanceof \Netdust\Loaders\Admin\Abstracts\SettingsField ) {
                                        echo $field->place( true );
                                    }
                                }
                                ?>

                                </tbody>
                            </table>
                            <?php wp_nonce_field( $template->get_param( 'nonce_action', '' ), 'underpin_nonce' ); ?>
                            <?php submit_button( 'Settings opslaan', 'small'); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="ld-overview">
            <div class="ld-overview--columns" >
                <div class="ld-overview--column ld-overview--widget table">
                    <h3>Product settings</h3>
                    <hr>
                    <h4>Taxonomy</h4>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=cat_type&post_type=product">Item Type</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=cat_product&post_type=product">Item Product</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=cat_sector&post_type=product">Item Sector</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=cat_doelgroep&post_type=product">Item Doelgroep</a><br>
                </div>
            </div>
        </div>


    </div>

</div>

