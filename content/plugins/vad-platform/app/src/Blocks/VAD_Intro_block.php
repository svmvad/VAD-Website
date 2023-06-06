<?php

namespace Netdust\VAD\Blocks;

use Netdust\Traits\Cache;
use Netdust\Traits\Setters;
use Netdust\Traits\Templates;

class VAD_Intro_block extends VAD_ACFBlock
{

    public function block_actions( ) {

        if( is_admin() ) {
            if( !get_field( 'intro_text' )  ) {
                ?>
                <!-- wp:paragraph -->
                <p class="font-size:small;color:grey;">
                    Klik hier om intro in te vullen, auteur kan hier ook worden opgegeven
                </p>
                <!-- /wp:paragraph -->
                <?php
            }
            if( get_field( 'intro_text' )  ) {
                ?>
                <!-- wp:paragraph -->
                <p class="font-size:large">
                    <?= $this->get_field( 'intro_text' ); ?>
                </p>
                <!-- /wp:paragraph -->
                <?php
            }
        }
        else {
            $this->echo_template();
        }
    }

    public function echo_template( ) {

        echo $this->get_template($this->name, [
                'title'=>get_the_title(),
                'text'=>$this->get_field( 'intro_text','' ),
                'image'=>get_the_post_thumbnail_url()
        ]);

    }

}