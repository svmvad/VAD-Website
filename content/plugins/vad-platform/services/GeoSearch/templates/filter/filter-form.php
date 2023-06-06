<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

$archive = false;
if( is_post_type_archive( 'vad--hulpverlening')||is_post_type_archive( 'vad--vroeginter')||is_post_type_archive( 'vad--preventie') ) {
    $archive = true;
}

?>

<form action="<?= esc_url( admin_url('admin-post.php') ); ?>" method="post" role="search" class="uk-form uk-grid-medium <?= $archive?'is-archive':'' ?>" uk-grid>
    <?php if( !$archive ) { ?>
    <input type="hidden" name="action" value="<?= $template->get_param( 'action', 'no_action' ); ?>">
    <?php } ?>

    <div class="uk-width-1-3@m vad-radio-control">
        <div class="uk-tile uk-tile-default uk-tile-xsmall">
            <label class="uk-form-label" for="hulpverlening">
                <input checked="checked" type="radio" value="hulpverlening" name="theme" id="hulpverlening" class="uk-radio" required <?= is_post_type_archive( 'vad--hulpverlening') ? 'checked':'' ?> >
                Hulpverlening
            </label>
        </div>
    </div>
    <div class="uk-width-1-3@m vad-radio-control">
        <div class="uk-tile uk-tile-default uk-tile-xsmall">
            <label class="uk-form-label" for="vroeginterventie">
                <input type="radio" value="vroeginterventie" name="theme" id="vroeginterventie" class="uk-radio" required <?= is_post_type_archive( 'vad--vroeginter') ? 'checked':'' ?> >
                Vroeginterventie
            </label>
        </div>
    </div>
    <div class="uk-width-1-3@m vad-radio-control">
        <div class="uk-tile uk-tile-default uk-tile-xsmall">
            <label class="uk-form-label" for="preventie">
                <input type="radio" value="preventie" name="theme" id="preventie" class="uk-radio" required <?= is_post_type_archive( 'vad--preventie') ? 'checked':'' ?> >
                Preventie
            </label>
        </div>
    </div>

    <div class="uk-width-1-1@m">
        <hr>
    </div>

    <div class="uk-width-1-3@m">
        <div class="uk-form-controls">
            <select
                    class="form-control uk-select"
                    name="geo"
                    id="geo"
                    placeholder="Zoek op stad of postcode"
                    required
            >
                <?= $template->get_param( 'postcodes', '' ); ?>

            </select>

        </div>
    </div>

    <div class="uk-width-1-3@m">
        <div class="uk-form-controls">
            <input type="text" value="<?= $template->get_param( 's', '' ); ?>" name="s" placeholder="Zoek op organisatienaam of adres" id="form-search" class="uk-input" >
        </div>
    </div>

    <?php wp_nonce_field( $template->get_param( 'nonce_action', '' ), 'vad_doorverwijsgids_nonce' ); ?>
    <div class="uk-width-1-3@m">
        <button type="submit" class="uk-button uk-button-default" >
            Keuze bevestigen
        </button>
    </div>
</form>