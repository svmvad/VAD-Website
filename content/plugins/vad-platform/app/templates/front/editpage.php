<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

?>

<div id="tm-main" class="tm-main uk-section uk-section-default" uk-height-viewport="expand: true" >
    <div class="uk-container">

        <h1><?= $template->get_param( 'title', 'Post Edit' ); ?></h1>
        <p><?= $template->get_param( 'description', '' ); ?></p>
        <?= $template->get_param( 'content', 'Er liep iets mis, geen post gevonden' ); ?>


    </div>
</div>