<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

$image = $template->get_param('background' );
$padding = $template->get_param('padding' );

$background = [
    'image'=>$image['bckgr_image' ],
    'color'=>$image['bckgr_color'],
    'text'=>$image['text_color'],
    'repeat'=>$image['bckgr_repeat' ]??'no-repeat',
    'position'=>$image['bckgr_position' ],
    'fixed'=>$image['bckgr_fixed' ]==1 ? 'fixed':'no-fixed',
];


$padding = [
    'size'=>$padding['padding-size' ]??'default',
    'remove'=>$padding['padding-remove' ]??'no-remove',
];

$style = ( filter_var($background['image'], FILTER_VALIDATE_URL) ) ? 'background-image: url("'.$background['image'].'");':'';
$attr = ( filter_var($background['image'], FILTER_VALIDATE_URL) ) ? 'uk-img data-src="'.$background['image'].'"' : '';

?>

<div class="uk-section uk-section-<?= strtolower( $background['color'] ); ?> <?= $background['text']!='None' ? 'uk-'.strtolower($background['text']):''; ?> ">
    <div <?= $attr; ?> class="uk-background-<?= $background['repeat']; ?> uk-background-<?= $background['position']; ?> uk-background-<?= $background['fixed']; ?> uk-section uk-section-<?= $padding['size']; ?> uk-padding-<?= $padding['remove']; ?>" style="<?= $style; ?>">

        <div class="uk-container">

            <div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid uk-grid-stack" uk-grid>
                <div class="uk-width-1-1 uk-first-column">

                    <blockquote class="uk-margin uk-text-center">
                        <p><?= $template->get_param('quote' ); ?>></p>
                        <footer class="el-footer" style="max-width: 100% !important;">
                            <?= $template->get_param('footer' ); ?>
                            <cite class="el-author"><?= $template->get_param('citation' ); ?></cite>
                        </footer>
                    </blockquote>

                </div>
            </div>
        </div>

    </div>

</div>