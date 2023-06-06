<?php
/**
 * Admin Page Template
 *
 * @author: Alex Standiford
 * @date  : 12/21/19
 */


use Netdust\Service\Pages\Admin\AdminPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $template ) || ! $template instanceof AdminPage ) {
	return;
}

$sections = $template->get_param( 'sections', [] );
$section  = $template->get_param( 'section', '' );

?>
<div class="ntdst-admin-page">
    <header class="ntdst-header">
        <div class="ntdst-container">
            <div class="ntdst-logo">
                <img src="<?php echo esc_url( app()->css_url( ) .'/img/logo.svg' ); ?>">
            </div>
            <div class="ntdst-float-right">
                <h2><?= $template->get_param( 'title', 'VAD Vormingen' ); ?></h2>
            </div>
        </div>
    </header>

<?php

if ( count( $sections ) > 1 ) {
	echo $template->get_template( 'admin-heading', [
		'section'   => $section,
		'sections'  => $sections,
		'menu_slug' => $template->get_param( 'menu_slug' ),
	] );
}

?>
    <div class="ntdst-notices-area">
        <div class="ntdst-container">

        </div>
    </div>

<?php if ( ! empty( $section ) && ! is_wp_error( $template->section( $section ) ) ):

    do_action('netdust-before_admin_content_area');
    ?>

    <main class="ntdst-content-area">
        <div class="ntdst-container">

            <?= $template->section( $section )->get_template( 'admin-section', [
                'view'      => $template->section( $section )->get_current_view_key(),
                'views'     => $template->section( $section )->get_views()
            ] );
            ?>

        </div>
    </main>
<?php
    do_action('netdust-after_admin_content_area');
endif; ?>
</div>