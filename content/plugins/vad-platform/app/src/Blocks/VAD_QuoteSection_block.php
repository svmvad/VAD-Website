<?php

namespace Netdust\VAD\Blocks;

use Netdust\Traits\Cache;
use Netdust\Traits\Templates;

class VAD_QuoteSection_block extends VAD_Section_block
{

    public function block_actions( ) {

        if( is_admin() ) {
            ?>
            <!-- wp:paragraph -->
            <blockquote>
                <?php
                echo $this->get_field( 'quote', 'Hier komt een quote' );
                ?>
            </blockquote>
            <!-- /wp:paragraph -->
            <?php
        }
        else {
            $this->echo_template();
        }
    }

    public function echo_template( ) {

        echo $this->get_template( $this->name, [
            'quote' => $this->get_field('quote' ),
            'citation' => $this->get_field('citation'),
            'footer' => $this->get_field('footer'),
            'background' => $this->get_field('section_background'),
            'padding' => $this->get_field('section_padding'),
        ]);

    }


}