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
$is_discount = isset($quantity_pricing["enable"]) && $quantity_pricing["type"] === "coupon";
$readable_discount = isset($quantity_pricing["enable"]) && $quantity_pricing["type"] === "coupon";


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
    $width = 240;
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
        </div>
    <?php endif ?>

    <?php
};

?>

    <div class="uk-margin uk-text-center uk-position-relative">
        <?php
        $image( get_the_post_thumbnail_url( get_the_ID() ), $attrs_image );

        if( get_field('preview_file') !== null) {
            ?>
            <a href="<?= get_field('preview_file') ?>" target="_blank" class="uk-icon-button uk-position-absolute" style="right:0;bottom:0;" uk-icon="eye"></a>
            <?php
        }
        ?>
    </div>

    <div class="uk-h5 uk-text-primary uk-margin-small" >Prijs</div>
    <div class="uk-panel tm-element-woo-price" >
        <p class="price"><?= $product->get_regular_price(); ?> € <?php echo $is_discount ? '( '. $quantity_pricing['coupon'][0]['discount'] / $product->get_regular_price() .' exemplaren gratis )' : ''; ?></p>
    </div>

    <div class="uk-hr" ></div>

    <?php  if ( $product->is_purchasable() ) { ?>
    <div class="uk-h5 uk-text-primary uk-margin-small" > Materiaal bestellen    </div>
    <div class="uk-panel tm-element-woo-add-to-cart uk-margin-small">
        <?php
        do_action('woocommerce_simple_add_to_cart');
        ?>
    </div>
    <div class="uk-margin-small uk-text-right">
        <a class="el-content" href="#" >
            Hoe verloopt het bestelproces
        </a>
    </div>
    <?php } ?>
    <?php  if ( $product->is_downloadable() ) { ?>
        <div class="uk-text-primary uk-h5">  Materiaal downloaden    </div>
        <div class="uk-margin-small uk-text-right">
    <?php
        $output= [];
// Loop through WC_Product_Download objects
        foreach( $product->get_downloads() as $key_download_id => $download ) {

            $download_name = $download->get_name(); // File label name
            $download_link = $download->get_file(); // File Url
            $download_id   = $download->get_id(); // File Id (same as $key_download_id)
            $download_type = $download->get_file_type(); // File type
            $download_ext  = $download->get_file_extension(); // File extension

            $output[$download_id] = '<a class="el-content uk-width-1-1 uk-button uk-button-primary uk-flex-inline uk-flex-center uk-flex-middle" href="'.$download_link.'"><span class="uk-margin-small-right uk-icon" uk-icon="download"></span> '. $download_name .' '. ( $download_ext != '' ? '(  '.$download_ext .' )':'' ) . '</a>';
        }
        // Output example
        echo implode('<br>', $output);
    ?>

        </div>
    <?php } ?>
