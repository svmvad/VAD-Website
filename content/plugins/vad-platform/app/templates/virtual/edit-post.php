<?php

use Netdust\VAD\Pages\VAD_EditForm;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) ) {
    return;
}

get_header();

$path = \Netdust\App::get( VAD_EditForm::class )->get_Path();

if( str_contains( $path, 'doorverwijsgids/preventie' ) )
    echo do_shortcode('[fluentform id="4"]' );

if( str_contains( $path, 'doorverwijsgids/hulpverlening' ) )
    echo do_shortcode('[fluentform id="4"]' );

if( str_contains( $path, 'doorverwijsgids/vroeginterventie' ) )
    echo do_shortcode('[fluentform id="4"]' );

if( str_contains( $path, 'onderzoeksdatabank' ) )
    echo do_shortcode('[fluentform id="6"]' );


get_footer();
