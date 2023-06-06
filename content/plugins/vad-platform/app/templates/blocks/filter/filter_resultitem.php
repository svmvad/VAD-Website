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

$layout = $template->get_field( 'filteritem_layout');

?>
<div>
<a class="el-item uk-card uk-card-default uk-card-small uk-card-hover uk-card-body uk-margin-remove-first-child uk-link-toggle uk-display-block uk-scrollspy-inview <?= $layout; ?>" href="<?= $template->get_param('permalink', '#' ); ?>" uk-scroll uk-scrollspy-class style aria-label="<?= $template->get_param('title', 'no post' ); ?>">

    <div class="uk-card-header">
    <div class="uk-child-width-expand uk-grid" uk-grid>
        <?php
        if( $layout !== 'no-image' ) { ?>
        <div class="uk-width-1-3@m uk-first-column">
            <?php
            $image( $template->get_param('image', '' ), $attrs_image );
            ?>
        </div>
        <?php } ?>
        <div class="uk-margin-remove-first-child">

            <div class="el-meta uk-text-meta uk-text-emphasis uk-margin-medium-top"><?= $template->get_param('meta', '' ); ?></div>
            <h2 class="el-title uk-h4 uk-text-primary uk-margin-small-top uk-margin-remove-bottom"> <?= $template->get_param('title', 'no post' ); ?></h2>

        </div>
    </div>
    </div>
    <div class="uk-card-body">
        <div class="el-content uk-panel uk-text-small uk-margin-top"><p><?= $template->get_param('excerpt', '' ); ?></p></div>
    </div>

</a>
</div>