<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

$logged_in = $template->get_param('logged_in', false );
$menu = $template->get_param('menu', 'Profile' );

?>

<div id="header-aside" class="header-aside">
    <div class="header-aside-inner">

        <?php
        if ( class_exists( 'WooCommerce' )  ) :
            echo $template->get_template('cart');
        endif;

        if ( $logged_in ) :
            ?>
            <nav class="user-wrap user-wrap-container menu-item-has-children">
                <?php
                $current_user = wp_get_current_user();
                $user_link  = get_author_posts_url( $current_user->ID );
                $display_name = $current_user->display_name;
                ?>

                <div uk-navbar class="uk-navbar">
                    <div class="uk-navbar-left">
                        <ul class="uk-navbar-nav">
                            <li>
                                <a class="user-link" href="#" role="button" aria-haspopup="true">
                                    <span class="user-name"><?php echo esc_html( $display_name ); ?></span><i uk-drop-parent-icon class="uk-icon uk-drop-parent-icon"></i>
                                    <?php
                                    echo get_avatar( get_current_user_id(), 35, '', '', ['class'=>'uk-border-circle'] );
                                    ?>
                                </a>
                                <div class="uk-navbar-dropdown">
                                    <ul class="uk-nav uk-navbar-dropdown-nav">
                                        <li>
                                            <a class="user-link" href="<?php echo esc_url( $user_link ); ?>">
                                                <?php echo get_avatar( get_current_user_id(), 35, '', '', ['class'=>'uk-border-circle'] ); ?>
                                                <span>
                                                <span class="user-name"><?php echo esc_html( $display_name ); ?></span>
                                                <span class="user-mention"><?php echo '@' . esc_html( $current_user->user_login ); ?></span>
                                            </span>
                                            </a>
                                        </li>
                                        <li class="uk-nav-divider"></li>
                                        <li>
                                        <?php
                                        wp_nav_menu(
                                            array(
                                                'menu' => $menu
                                            )
                                        );

                                        ?>
                                        </li>
                                    </ul>

                                </div>
                            </li>

                        </ul>

                    </div>
                </div>

            </nav>
        <?php
        endif;

        if ( ! $logged_in ) :

            ?>

            <div class="bb-header-buttons">
                <a href="<?php echo esc_url( wp_login_url() ); ?>" class="uk-button uk-button-link"><?php esc_html_e( 'Sign in', 'vad_platform' ); ?></a>

                <?php if ( get_option( 'users_can_register' ) ) : ?>
                    <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="uk-button uk-button-default uk-button-small"><?php esc_html_e( 'Sign up', 'vad_platform' ); ?></a>
                <?php endif; ?>
            </div>

        <?php

        endif;

        ?>

    </div><!-- .header-aside-inner -->
</div><!-- #header-aside -->
