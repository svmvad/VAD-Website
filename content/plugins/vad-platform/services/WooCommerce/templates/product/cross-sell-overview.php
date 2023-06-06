<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

$results = $template->get_param('products',[] );

?>

<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-3@m uk-grid-small uk-grid-match uk-grid ntdst-filter-results" uk-grid uk-scrollspy="target: [uk-scrollspy-class]; cls: uk-animation-slide-bottom-medium; delay: false;">

    <?php
    foreach ( $results as $result ): ?>
        <div  <?= $result === key($results) ? 'class="uk-first-column"':''; ?> >
            <?= $template->get_template( 'parts/product-preview', [
                'product'=>$result
            ] ); ?>
        </div>
    <?php endforeach; ?>

</div>
