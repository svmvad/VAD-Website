<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

$taxonomies = $template->get_param('taxonomies',[] );
$results = $template->get_param('results',[] );
$count = count ( $taxonomies );

?>

<div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid uk-grid-stack ntdst-filter" uk-grid>

    <div class="uk-grid-item-match uk-first-column">
        <div class="uk-tile-muted uk-tile uk-tile-small">

            <div>
                <form action="<?= esc_url( admin_url('admin-post.php') ); ?>" method="post" role="search" class="uk-form uk-search uk-search-default uk-width-1-1">
                    <button uk-search-icon="" class="uk-search-icon-flip uk-icon uk-search-icon"></button>
                    <input type="hidden" name="filter_excl_categories" value="product_cat,product_type">
                    <input value="<?= $template->get_param( 's', '' ); ?>" name="s" type="search" placeholder="Search" class="uk-search-input">
                </form>
            </div>

            <div class="uk-margin">
                <div class="uk-child-width-1-1 uk-child-width-1-<?= $count; ?>@m uk-grid-match uk-grid ntdst-categories" uk-grid>
                    <?php

                    foreach ( $taxonomies as $slug => $taxonomy ):
                        ?>
                        <input type="hidden" id="filters-<?= $slug; ?>" value="" />
                        <div id="tax-<?= $slug; ?>" >
                            <h3 uk-toggle="target: #cat-filter-<?= $slug; ?>"  >
                                <?= $taxonomy['label']; ?>
                                <span class="uk-margin-small-left uk-icon" uk-icon="triangle-down"></span>
                            </h3>
                            <div class="filter-active uk-margin-bottom">

                            </div>
                            <ul id="cat-filter-<?= $slug; ?>" class="uk-margin-remove-bottom uk-nav uk-nav-default" hidden>
                                <?php
                                foreach ( $taxonomy['terms'] as $term => $label ): ?>
                                    <li>
                                        <a href="javascript:;" class="filter-link" data-term="<?= $term; ?>" data-cat="<?= $slug; ?>" >
                                            <span class="uk-margin-small-left uk-margin-small-right uk-icon" uk-icon="plus"></span>
                                            <?= $label; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>

                </div>

            </div>

        </div>

    </div>
</div>

<?= $template->get_template( 'filter_result', [ 'results'=>$results ] ); ?>

