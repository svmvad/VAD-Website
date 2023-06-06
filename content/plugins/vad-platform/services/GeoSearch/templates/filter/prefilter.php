<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

?>

<div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid ntdst-filter" uk-grid>

    <div class="uk-grid-item-match uk-first-column">

        <div class="uk-tile-muted uk-tile uk-tile-small">

            <div>
                <?= $template->get_template( 'filter-form', [
                        'action'=>$template->get_param( 'action', 'no_action' ),
                        'postcodes'=>$template->get_param( 'postcodes', '' )
                ] ); ?>
            </div>

        </div>

    </div>
</div>


