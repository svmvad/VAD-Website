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

// check if the repeater field has rows of data
if( have_rows('add_downloads') ):

    ?>

            <div class="uk-grid tm-grid-expand uk-grid-margin" uk-grid>
                <div class="uk-width-2-5@m uk-first-column">
                    <h2 class="uk-text-secondary uk-position-relative">
                        <?= $template->get_param('title', 'Verdiepende downloads'); ?>
                    </h2>
                    <div class="uk-panel uk-text-secondary uk-margin">
                        <?= $template->get_param('description', ''); ?>
                    </div>
                </div>
                <div class="uk-width-3-5@m">

                    <div class="uk-panel">
                        <div uk-accordion="collapsible: false;" class="uk-accordion uk-margin-large-top" id="product-download-list">
                            <?php

                            // loop through the rows of data
                            while ( have_rows('add_downloads') ) : the_row();
                                $index = get_row_index();
                                ?>
                                <div>

                                    <a id="<?= $index; ?>" class="uk-accordion-title uk-box-shadow-small" href="#" aria-expanded="true"><?php the_sub_field('download_group_label'); ?></a>

                                    <div class="uk-accordion-content">
                                        <?php
                                        $downloads_tekst = get_sub_field('downloads_tekst');
                                        if( $downloads_tekst ): ?>
                                        <div class="uk-panel uk-margin">
                                            <?= $downloads_tekst; ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="uk-panel">
                                            <ul class="uk-list">
                                                <?php
                                                $downloads = get_sub_field('downloads');
                                                if( $downloads ): ?>
                                                    <?php foreach( $downloads as $post_id): ?>
                                                        <li class="el-item">
                                                            <div class="uk-grid-small uk-child-width-expand uk-flex-nowrap uk-flex-middle uk-grid" uk-grid>
                                                                <div class="uk-width-auto uk-first-column">
                                                                    <span class="el-image uk-icon" uk-icon="icon: <?= get_sub_field('icon_select'); ?>;"></span>
                                                                </div>
                                                                <div>
                                                                    <div class="el-content uk-panel">
                                                                        <a href="<?= get_the_permalink($post_id); ?>"><?= get_the_title($post_id); ?></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>

                                            </ul>
                                        </div>

                                    </div>
                                </div>

                            <?php

                            endwhile;

                            ?>
                        </div>
                    </div>

                </div>
            </div>

<?php

endif;

