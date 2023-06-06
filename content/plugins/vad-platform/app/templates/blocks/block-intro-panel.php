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

    if (!$src = $thumbnal_url ) {
        return;
    }

    $image = app(ImageProvider::class);
    $meta = get_post_meta(get_post_thumbnail_id());
    $src = Url::relative(set_url_scheme($src, 'relative'));
    $alt = $meta['_wp_attachment_image_alt'] ?? '';
    $width = 400;
    $height = 'auto';

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
            <figcaption class="uk-text-meta"><?= get_the_post_thumbnail_caption(); ?></figcaption>
        </div>
    <?php endif ?>

    <?php
};


?>

<div class="vad-hero-header uk-section-muted uk-section">

    <div class="uk-container">

        <div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid uk-grid-stack" uk-grid>
            <div class="uk-width-1-1 uk-first-column">

                <div>
                    <ul class="uk-breadcrumb">

                        <li><a href="/">Home</a></li>
                        <li><a href="/artikels/">Artikels</a></li>
                        <li><span><?= $template->get_param('title' ); ?></span></li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="tm-grid-expand uk-grid-margin uk-grid" uk-grid>
            <div class="uk-width-3-5@m uk-first-column">

                <h1 class="uk-margin-remove-bottom uk-text-left"><?= $template->get_param('title' ); ?></h1>
                <div class="uk-margin-small uk-text-left">
                    <ul class="uk-margin-remove-bottom uk-subnav  uk-subnav-divider uk-flex-left" uk-margin>
                        <li class="el-item uk-first-column">
                            <a class="el-content uk-disabled"><?= get_the_modified_date() ?></a>
                        </li>
                        <li class="el-item ">
                            <?php
                            $terms = get_terms([
                                'taxonomy' => 'onderwerp',
                                'orderby' => 'count',
                                'hide_empty' => true,
                            ]);
                            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                                $count = count( $terms );
                                $i = 0;
                                $term_list = '<div class="my_term-archive">';
                                foreach ( $terms as $term ) {
                                    $i++;
                                    $term_list .= '<a href="' . esc_url( get_term_link( $term ) ) . '" alt="' . esc_attr( sprintf( __( 'View all post filed under %s', 'my_localization_domain' ), $term->name ) ) . '">' . $term->name . '</a>';
                                    if ( $count != $i ) {
                                        $term_list .= ' &middot; ';
                                    }
                                    else {
                                        $term_list .= '</div>';
                                    }
                                }
                                echo $term_list;
                            }
                            ?>
                        </li>
                    </ul>
                </div>
                <div class="uk-panel uk-text-lead uk-margin"><?= $template->get_param('text' ); ?></div>

            </div>

            <div class="uk-width-2-5@m">

                <div class="uk-margin">
                    <?php
                    if( $template->get_param('image' ) != ''  )
                        $image( $template->get_param('image' ), $attrs_image  );
                    ?>
                </div>

            </div>
        </div>

    </div>

</div>