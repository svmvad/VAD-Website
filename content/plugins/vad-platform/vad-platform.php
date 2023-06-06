<?php
/**
 *
 * @link              https://netdust.be
 * @since             1.0.0-dev
 * @package           Netdust\Vad
 * @author            Stefan Vandermeulen
 *
 * @wordpress-plugin
 * Plugin Name:       VAD Online Platform
 * Plugin URI:        https://netdust.be
 * Description:       A framework for VAD Online Wordpress Applications.
 * Version:           3.0.0
 * Author:            Stefan Vandermeulen
 * Author URI:        https://netdust.be
 * Text Domain:       vad_platform
 */

/**
 * @todo FAQ op thema pagina's met structured data
 * @todo auto redirect na veranderen slug 301
 * @todo thema's opsplitsen voor de doorverwijsgids fiches en optie andere toevoegen
 * @todo vroeginterventie doelgroepen tekstvelden toevoegen
 * @todo preventie en vroeginterventie zoekveld weglaten, enkel postcodes
 *
 * @todo teaser for products, order for products
 * @todo pagina links veranderen naar tools voor preventiewerk
 * @todo content links = 1 artikel
 *
 */


defined( 'ABSPATH' ) || exit;

define( 'APP_PLUGIN_FILE', __FILE__ );

use Netdust\App;

function vad_is_website(){
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

    if (strpos($url,'vormingen') !== false) {
        return FALSE;
    }

    return TRUE;
}

//boot application
App::boot( NTDST_APPLICATION, [
    'file'                => APP_PLUGIN_FILE,
    'text_domain'         => 'ntdst',
    'version'             => '2.0.0',
    'minimum_wp_version'  => '6.0',
    'minimum_php_version' => '7.4',
    'build_path'          => '/app'
] );

