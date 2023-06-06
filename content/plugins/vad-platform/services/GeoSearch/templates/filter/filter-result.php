<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

$results = $template->get_param('results',[] );

?>
<div class="uk-text-meta uk-heading-line uk-text-center uk-margin-small"><span id="result-count">Gevonden aantal resultaten: <?= count($results); ?></span></div>
<div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid uk-grid-stack ntdst-filter-results" uk-grid>

    <?php

    if( count($results) == 0 ) {
    ?><h5>Jammer, we hebben geen resultaten gevonden.</h5><?php
    }
    else
    foreach ( $results as $result ): ?>
        <div  <?= $result === key($results) ? 'class="uk-first-column"':''; ?> >
            <?= $result; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php do_action( 'netdust_pagination', count($results), 15 ); ?>