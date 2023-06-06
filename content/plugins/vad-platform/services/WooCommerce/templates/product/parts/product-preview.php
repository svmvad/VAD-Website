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


[$config, $view] = app(\YOOtheme\Config::class, \YOOtheme\View::class);

// Image
$attrs_image['class'][] = 'uk-text-center';

// Image template, see templates/post/content
$image = function ($thumbnal_url, $attr) use ($config, $view) {

    if (!$src = $thumbnal_url) {
        return;
    }

    $image = app(ImageProvider::class);
    $meta = get_post_meta(get_post_thumbnail_id());
    $src = Url::relative(set_url_scheme($src, 'relative'));
    $alt = $meta['_wp_attachment_image_alt'] ?? '';
    $width = 122;
    $height = 180;

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

$product_id = $template->get_param('product', null );
$terms = wp_get_post_terms( get_the_id(), 'product_tag' );

?>

<a class="el-item uk-card uk-card-default uk-card-small uk-card-hover uk-card-body uk-margin-remove-first-child uk-link-toggle uk-display-block" href="<?= $template->get_param('permalink', '#' ); ?>" uk-scroll uk-scrollspy-class style aria-label="<?= $template->get_param('title', 'no post' ); ?>">

    <div class="uk-card-header">
        <div class="uk-child-width-expand uk-grid" uk-grid>
            <div class="uk-width-1-3@m uk-first-column">
                <?php
                $image( get_the_post_thumbnail_url( $product_id ), $attrs_image );
                ?>
            </div>
            <div class="uk-margin-remove-first-child">

                <div class="el-meta uk-text-meta uk-text-emphasis uk-margin-medium-top"><?= $terms[0]->name; ?></div>
                <h2 class="el-title uk-h4 uk-text-primary uk-margin-small-top uk-margin-remove-bottom"> <?= get_the_title( $product_id ); ?></h2>

            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <div class="el-content uk-panel uk-text-small uk-margin-top"><p><?= get_the_excerpt( $product_id )?></p></div>
    </div>

</a>
