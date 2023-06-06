<?php
/**
 * Admin Section Template
 *
 * @author: Alex Standiford
 * @date  : 12/21/19
 */

use Netdust\Loaders\Admin\Abstracts\AdminSection;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $template ) || ! $template instanceof AdminSection ) {
    return;
}
?>

