<?php

use YOOtheme\ImageProvider;
use YOOtheme\Url;
use function YOOtheme\app;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

$product = wc_get_product( get_the_ID() );
$quantity_pricing = get_post_meta(get_the_ID(), "o-discount", true);


?>

<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}


// check if the repeater field has rows of data
if( have_rows('add_downloads') ):

    ?>
    <div uk-accordion="collapsible: true;" class="uk-accordion uk-margin-large-top" id="product-download-list">
        <?php

        // loop through the rows of data
        while ( have_rows('add_downloads') ) : the_row();
            $index = get_row_index();
            ?>
            <div class="uk-open">

                <a id="<?= $index; ?>" class="uk-accordion-title uk-box-shadow-small" href="#" aria-expanded="true"><?php the_sub_field('download_group_label'); ?></a>

                <div class="uk-accordion-content">

                    <div class="uk-panel">
                        <ul class="uk-list">
                            <?php
                            $downloads = get_sub_field('downloads');
                            if( $downloads ): ?>
                                    <?php foreach( $downloads as $post): // variable must be called $post (IMPORTANT) ?>
                                        <?php setup_postdata($post); ?>
                                        <li class="el-item">
                                            <div class="uk-grid-small uk-child-width-expand uk-flex-nowrap uk-flex-middle uk-grid" uk-grid>
                                                <div class="uk-width-auto uk-first-column">
                                                    <span class="el-image uk-icon" uk-icon="icon: download;"></span>
                                                </div>
                                                <div>
                                                    <div class="el-content uk-panel">
                                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php wp_reset_postdata(); ?>
                            <?php endif; ?>

                        </ul>
                    </div>

                </div>
            </div>

        <?php

        endwhile;

        ?>
    </div>
<?php

endif;

