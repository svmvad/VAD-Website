<?php

use YOOtheme\ImageProvider;
use YOOtheme\Url;
use function YOOtheme\app;

if (!defined('ABSPATH')) {
    exit;
}

if (!isset($template)) {
    return;
}

[$config, $view] = app(\YOOtheme\Config::class, \YOOtheme\View::class);

// Image
$attrs_image['class'][] = 'uk-text-center';

// Image template, see templates/post/content
$image = function ($thumbnal_url, $attr, $width=390, $height=290 ) use ($config, $view) {

    if (!$src = $thumbnal_url ) {
        return;
    }

    $image = app(ImageProvider::class);
    $meta = get_post_meta(get_post_thumbnail_id());
    $src = Url::relative(set_url_scheme($src, 'relative'));
    $alt = $meta['_wp_attachment_image_alt'] ?? '';

    if ($view->isImage($src) == 'svg') {
        $thumbnail = $image->replace($view->image($src, ['width' => $width, 'height' => $height, 'loading' => 'lazy', 'alt' => $alt]));
    } else {
        $thumbnail = $image->replace($view->image([$src, 'thumbnail' => [$width, $height], 'srcset' => true], ['loading' => 'lazy', 'alt' => $alt]));
    }

    ?>

    <?php if ($thumbnail) : ?>
        <div<?= $view->attrs($attr) ?> property="image" typeof="ImageObject">
            <meta property="url" content="<?= $thumbnal_url ?>">
            <?= $thumbnail ?>
        </div>
    <?php endif ?>

    <?php
};

$articlesgroups = get_field('related_artikels' );

$add_section = true;

?>

<?php if( isset( $add_section ) && $add_section ) { ?>
    <div class="vad-section-related-articles uk-section-primary uk-section">
    <div class="uk-container">
<?php } ?>



<div class="tm-grid-expand uk-grid-margin uk-grid" uk-grid>
    <div class="uk-grid-item-match uk-flex-middle uk-width-1-2@m uk-first-column">
        <div class="uk-panel uk-width-1-1">
            <h2>Gerelateerde artikelen</h2>
        </div>
    </div>

    <div class="uk-grid-item-match uk-flex-middle uk-width-1-2@m">

        <div class="uk-panel uk-width-1-1">

            <div class="uk-margin uk-text-right">

                <a class="el-content uk-flex-inline uk-flex-center uk-flex-middle" href="/artikels/">
                    <span class="uk-margin-small-right uk-icon" uk-icon="search"></span>
                    Doorzoek al onze publicaties en vind informatie op jouw maat
                </a>

            </div>

        </div>

    </div>
</div>

<div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid uk-grid-stack">
    <div class="uk-width-1-1 uk-first-column">

        <hr>

    </div>
</div>

<?php

foreach ( $articlesgroups as $group) :

    if( isset( $group['size'] ) && $group['size'] == 'small' ) {
        ?>
        <div class="tm-grid-expand uk-grid-margin uk-grid" uk-grid>
            <div class="uk-width-1-1 uk-first-column">

                <div class="uk-margin">
                    <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-grid-match uk-grid uk-grid-stack" uk-grid>
                        <?php foreach ( $group['artikels'] as $articles_id ) : ?>
                            <div <?php if($articles_id===$group['artikels'][0]) { echo 'class="uk-first-column"'; } ?> >
                                <div class="el-item uk-panel uk-margin-remove-first-child">
                                    <div class="uk-width-1-2@m uk-flex-first@m">
                                        <?php
                                        $image( get_the_post_thumbnail_url( $articles_id ), $attrs_image, 360, 290 );
                                        ?>
                                    </div>
                                    <div class="uk-margin-remove-first-child uk-first-column">

                                        <h3 class="el-title uk-h5 uk-text-primary uk-margin-top uk-margin-remove-bottom">
                                            <?= get_the_title( $articles_id ); ?>
                                        </h3>
                                        <div class="el-meta uk-text-meta uk-text-emphasis uk-margin-small-top uk-text-uppercase">
                                            <?php
                                            $terms = wp_get_post_terms( $articles_id, 'onderwerp' );
                                            foreach( $terms as $key => $term){
                                                echo $term->name.( $key < count($terms)-1 ?', ':'' );
                                            }
                                            ?>
                                        </div>
                                        <div class="el-content uk-panel uk-text-small uk-margin-top">
                                            <?= get_the_excerpt( $articles_id ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    if( isset( $group['size'] ) && $group['size'] == 'medium' ) {
?>
        <div class="tm-grid-expand uk-grid-margin uk-grid" uk-grid>
            <div class="uk-width-1-1 uk-first-column">

                <div class="uk-margin">
                    <div class="uk-child-width-1-1 uk-child-width-1-4@m uk-grid-match uk-grid uk-grid-stack" uk-grid>
                        <?php foreach ( $group['artikels'] as $articles_id ) : ?>
                        <div <?php if($articles_id===$group['artikels'][0]) { echo 'class="uk-first-column"'; } ?> >
                            <div class="el-item uk-panel uk-margin-remove-first-child">

                                <?= $image( get_the_post_thumbnail_url( $articles_id ), $attrs_image, 240, 130 ); ?>

                                <h3 class="el-title uk-h5 uk-text-primary uk-margin-top uk-margin-remove-bottom">
                                    <?= get_the_title( $articles_id ); ?>
                                </h3>
                                <div class="el-meta uk-text-meta uk-text-emphasis uk-margin-small-top uk-text-uppercase">
                                    <?php
                                    $terms = wp_get_post_terms( $articles_id, 'onderwerp' );
                                    foreach( $terms as $key => $term){
                                        echo $term->name.( $key < count($terms)-1 ?', ':'' );
                                    }
                                    ?>
                                </div>
                                <div class="el-content uk-panel uk-text-small uk-margin-top">
                                    <?= get_the_excerpt( $articles_id ); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
<?php
    }

    if( isset( $group['size'] ) && $group['size'] == 'large' ) {

        $main_article = array_shift( $group['artikels'] );
        $last_articles = array_slice($group['artikels'], 4);
        $next_articles = array_slice($group['artikels'], 0, 4);
?>

<div class="tm-grid-expand uk-grid-margin uk-grid" uk-grid>
    <div class="uk-width-2-3@m uk-first-column">

        <div class="uk-panel uk-margin-remove-first-child uk-margin">

            <div class="uk-child-width-expand uk-grid" uk-grid>
                <div class="uk-width-1-2@m uk-flex-last@m">
                    <?php
                    $image( get_the_post_thumbnail_url( $main_article ), $attrs_image, 360, 290 );
                    ?>
                </div>
                <div class="uk-margin-remove-first-child uk-first-column">

                    <h3 class="el-title uk-h4 uk-text-primary uk-margin-top uk-margin-remove-bottom">
                    <?= get_the_title( $main_article ); ?>
                    </h3>
                    <div class="el-meta uk-text-meta uk-text-emphasis uk-margin-small-top uk-text-uppercase">
                    <?php
                        $terms = wp_get_post_terms( $main_article, 'onderwerp' );
                        foreach( $terms as $key => $term){
                            echo $term->name.( $key < count($terms)-1 ?', ':'' );
                        }
                    ?>
                    </div>
                    <div class="el-content uk-panel uk-text-small uk-margin-top">
                    <?=  get_the_excerpt( $main_article ); ?>
                    </div>

                </div>
            </div>


        </div>
        <hr>
        <div class="uk-margin">
            <div class="uk-child-width-1-1 uk-child-width-1-3@m uk-grid-match uk-grid uk-grid-stack" uk-grid>
                <?php foreach ( $next_articles as $articles_id ) : ?>
                <div <?php if($articles_id===$next_articles[0]) { echo 'class="uk-first-column"'; } ?> >
                    <div class="el-item uk-panel uk-margin-remove-first-child">

                        <?= $image( get_the_post_thumbnail_url( $articles_id ), $attrs_image, 240, 130 ); ?>

                        <h3 class="el-title uk-h5 uk-text-primary uk-margin-top uk-margin-remove-bottom">
                            <?= get_the_title( $articles_id ); ?>
                        </h3>
                        <div class="el-meta uk-text-meta uk-text-emphasis uk-margin-small-top uk-text-uppercase">
                            <?php
                            $terms = wp_get_post_terms( $articles_id, 'onderwerp' );
                            foreach( $terms as $key => $term){
                                echo $term->name.( $key < count($terms)-1 ?', ':'' );
                            }
                            ?>
                        </div>
                        <div class="el-content uk-panel uk-text-small uk-margin-top">
                            <?= get_the_excerpt( $articles_id ); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <div class="uk-width-1-3@m">

        <div class="uk-margin">
            <div class="uk-child-width-1-1 uk-grid-match uk-grid uk-grid-stack">
                <?php
                foreach ( $last_articles as $articles_id ) :
                ?>
                <div <?php if($articles_id===$last_articles[0]) { echo 'class="uk-first-column"'; } ?> >
                    <div class="el-item uk-panel uk-margin-remove-first-child">

                        <h3 class="el-title uk-h5 uk-text-primary uk-margin-top uk-margin-remove-bottom">
                            <?= get_the_title( $articles_id ); ?>
                        </h3>
                        <div class="el-meta uk-text-meta uk-text-emphasis uk-margin-small-top uk-text-uppercase">
                            <?php
                            $terms = wp_get_post_terms( $articles_id, 'onderwerp' );
                            foreach( $terms as $key => $term){
                                echo $term->name.( $key < count($terms)-1 ?', ':'' );
                            }
                            ?>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<?php
    }
endforeach;
    ?>

<?php if( isset( $add_section ) && $add_section ) { ?>
    </div>
    </div>
<?php } ?>