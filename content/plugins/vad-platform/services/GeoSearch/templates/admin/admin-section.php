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
                    <h3>Hulpverlening settings</h3>
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
                    <hr>
                    <h4>Taxonomy</h4>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=doorverwijsgids_themas">Hulpverlening Thema's</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=hulpverlening_leeftijd">Hulpverlening Leeftijd</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=hulpverlening_ambulant">Hulpverlening Ambulant</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=hulpverlening_semi_residentieel">Hulpverlening Semi Residentieel</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=hulpverlening_residentieel">Hulpverlening Residentieel</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=hulpverlening_omgeving">Hulpverlening Omgeving</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=hulpverlening_welzijn_en_advies">Hulpverlening Welzijn en advies</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=hulpverlening_behandeling_therapie">Hulpverlening Behandeling/Therapie</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=hulpverlening_overige_werkvorm">Hulpverlening Overige werkvorm</a><br>
                </div>
            </div>
        </div>
        <div class="ld-overview">
            <div class="ld-overview--columns" >
                <div class="ld-overview--column ld-overview--widget table">
                    <h3>Vroeginterventie settings</h3>
                    <hr>
                    <h4>Taxonomy</h4>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=vroeginterventie_aanbod">Vroeginterventie Aanbod</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=vroeginterventie_doelgroep">Vroeginterventie Doelgroepen</a><br>
                </div>
            </div>
        </div>
        <div class="ld-overview">
            <div class="ld-overview--columns" >
                <div class="ld-overview--column ld-overview--widget table">
                    <h3>Preventie settings</h3>

                    <hr>
                    <h4>Taxonomy</h4>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=preventie_problem">Preventie Problematiek</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=preventie_doelgroep">Preventie Doelgroep</a><br>
                    <a class="btn" href="/wp-admin/edit-tags.php?taxonomy=preventie_sector">Preventie Sector</a><br>
                </div>
            </div>
        </div>

    </div>

</div>

