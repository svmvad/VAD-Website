<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}


$section_background = $template->get_param('background' );
$section_styles = $template->get_param('padding' );
$product = $template->get_param('product' );
$linked = $template->get_param('linked' );

$background = [
    'image'=>$section_background['bckgr_image' ],
    'color'=>$section_background['bckgr_color'],
    'text'=>$section_background['text_color'],
    'repeat'=>$section_background['bckgr_repeat' ]??'no-repeat',
    'position'=>$section_background['bckgr_position' ],
    'fixed'=>$section_background['bckgr_fixed' ]==1 ? 'fixed':'no-fixed',
];


$padding = [
    'size'=>$section_styles['padding-size' ]??'default',
    'remove'=>$section_styles['padding-remove' ]??'no-remove',
];

$style = ( filter_var($background['image'], FILTER_VALIDATE_URL) ) ? 'background-image: url("'.$background['image'].'");':'';
$attr = ( filter_var($background['image'], FILTER_VALIDATE_URL) ) ? 'uk-img data-src="'.$background['image'].'"' : '';

?>


<div class="uk-section uk-padding-<?= $padding['remove']; ?> uk-section-<?= strtolower( $background['color'] ); ?> <?= $background['text']!='None' ? 'uk-'.strtolower($background['text']):''; ?> ">
    <div <?= $attr; ?> class="uk-background-<?= $background['repeat']; ?> uk-background-<?= $background['position']; ?> uk-background-<?= $background['fixed']; ?>" style="<?= $style; ?>">

        <div class="uk-container">

            <div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid uk-grid-stack" uk-grid>
                <div class="uk-width-1-1 uk-first-column uk-position-relative">

                    <div class="uk-panel uk-margin uk-text-left">
                        <span class="title-wrapper"><h2> <?= $template->get_param('title' ); ?></h2></span>
                        <?= $template->get_param('text' ); ?>
                    </div>

                    <?php if( isset( $product['item'] ) && !empty($product['item']) ) : ?>
                        <div class="uk-card uk-position-top-<?= $product['align']; ?> uk-box-shadow-small uk-padding-small uk-text-meta" >
                            <div class="uk-h5">Materialen vermeld in dit artikel</div>
                            <div class="uk-h6"><?= $product['item']->post_title; ?></div>
                            <p><?= $product['item']->post_excerpt; ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if( isset( $linked['title']) && !empty( $linked['title'] )
                           || isset( $linked['text']) && !empty( $linked['text'] )
                    ) : ?>
                    <div class="uk-card uk-position-top-<?= $linked['align']; ?> uk-box-shadow-small uk-padding-small uk-text-meta">
                        <?= isset($linked['title'])? '<h5>'.$linked['title'].'</h5>':''; ?>
                        <?= isset($linked['text'])? '<p>'.$linked['text'].'</p>':''; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>


        </div>

    </div>
</div>