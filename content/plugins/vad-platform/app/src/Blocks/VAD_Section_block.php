<?php

namespace Netdust\VAD\Blocks;

use Netdust\Traits\Cache;
use Netdust\Traits\Setters;
use Netdust\Traits\Templates;

class VAD_Section_block extends VAD_ACFBlock
{

    public function block_actions( ) {

        if( is_admin() ) {
            ?>
            <!-- wp:paragraph -->
            <h2>
                <?=
                 $this->get_field( 'section_title', 'Geef een titel aan deze sectie');
                ?>
            </h2>
            <p>
                <?=
                $this->get_field( 'section_text', 'Sectie tekst');
                ?>
            </p>
            <!-- /wp:paragraph -->
            <?php
        }
        else {
            $this->echo_template();
        }
    }

    public function echo_template( ) {

        echo $this->get_template( $this->name, [
                'title' => $this->get_field('section_title' ),
                'text' => $this->get_field('section_text' ),
                'product' => [
                    'item' => $this->get_field('catalogus_item'),
                    'align' => $this->get_field('align_cat_item'),
                ],
                'linked' => [
                    'title' => $this->get_field('linked_title'),
                    'text' => $this->get_field('linked_text'),
                    'align' => $this->get_field('linked_align'),
                ],
                'background' => $this->get_field('section_background'),
                'padding' => $this->get_field('section_padding')
        ]);

    }

}