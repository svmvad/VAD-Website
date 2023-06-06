<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

?>

<div id="site-header-cart" class="notification-wrap site-header-cart menu" >
    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="header-cart-link notification-link" role="button" aria-haspopup="true">
        <i class="uk-icon" uk-icon="cart"></i>
        <?php
        if ( is_object( WC()->cart ) ) {
            $wc_cart_count = wc()->cart->get_cart_contents_count();
            if( $wc_cart_count != 0 ) { ?>
                <span class="count"><?php echo wc()->cart->get_cart_contents_count(); ?></span>
            <?php }
        }
        ?>
    </a>
    <div class="uk-navbar-dropdown widget woocommerce widget_shopping_cart" style="min-width: 350px;">
        <header class="notification-header widget_shopping_cart_content">
            <div class="title uk-h5"><?php esc_html_e( 'Shopping Cart', 'buddyboss-theme' ); ?></div>
        </header>
        <div class="header-mini-cart">
            <?php
            if ( is_object( WC()->cart ) ) {
                woocommerce_mini_cart();
            }
            ?>
        </div>
    </div>
</div>