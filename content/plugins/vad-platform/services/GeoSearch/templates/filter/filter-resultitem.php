<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

$layout = $template->get_field( 'filteritem_layout');

?>

<div class="uk-margin">
    <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-flex-middle uk-grid uk-flex-top uk-flex-wrap-top uk-grid-stack" uk-grid="masonry: 1;" >
        <div class="uk-first-column" >
            <a class="el-item uk-card uk-card-hover uk-card-small uk-flex uk-link-toggle" href="<?= $template->get_param('permalink', '#' ); ?>">
                <div class="uk-child-width-expand uk-grid-collapse uk-grid-match uk-grid" uk-grid>
                    <?php
                    if( $layout === 'With Image') {
                    ?>
                        <div class="uk-width-2-5 uk-first-column">
                            <div class="uk-card-media-left uk-cover-container">
                                <?= $template->get_param('image', '' ); ?>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                    <div>
                        <div class="uk-card-body uk-margin-remove-first-child">

                            <?php if( $layout !== 'Minimal Panel') { ?>
                            <h3 class="el-title uk-card-title uk-margin-top uk-margin-remove-bottom"><?= $template->get_param('title', 'no post' ); ?></h3>
                            <?php }else{ ?>
                            <h4 class="el-title uk-card-title uk-margin-top uk-margin-remove-bottom"><?= $template->get_param('title', 'no post' ); ?></h4>
                            <!--<div class="el-meta uk-text-meta uk-margin-top"><?= $template->get_param('meta', '' ); ?></div>-->
                            <?php }
                            if( $layout !== 'Minimal Panel') {
                            ?>
                            <div class="el-content uk-panel uk-margin-top uk-text-meta"><?= $template->get_param('excerpt', '' ); ?></div>
                            <div class="uk-margin-top"><div class="el-link uk-link">Lees meer</div></div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

