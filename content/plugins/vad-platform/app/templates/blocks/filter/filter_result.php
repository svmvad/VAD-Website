<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

$results = $template->get_param('results',[] );
$layout = $template->get_field( 'filterresult_layout');

$class = "uk-child-width-1-1 uk-child-width-1-2@s";
if( $layout == "3-row" ) $class .=" uk-child-width-1-3@m";

?>

    <div class="uk-text-meta uk-text-emphasis uk-heading-line uk-text-center uk-margin-large"><span>Gevonden aantal resultaten: <span id="result-count"><?= $results['json']['total']; ?></span></span></div>

<div class="<?= $class; ?> uk-grid-small uk-grid-match uk-grid ntdst-filter-results" uk-grid uk-scrollspy="target: [uk-scrollspy-class]; cls: uk-animation-slide-bottom-medium; delay: false;">

    <?php
    foreach ( $results['data'] as $result ): ?>

            <?= $result; ?>

    <?php endforeach; ?>

</div>


<?php
