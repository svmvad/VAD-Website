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


<div >

    <div class="ld-bootview">

        <div class="ld-overview">
            <div class="ld-overview--columns" >
                <div class="ld-overview--column ld-overview--widget table">
                    <h3>memberflow settings</h3>
                    <hr>
                    ok
                </div>
            </div>
        </div>

    </div>

</div>

