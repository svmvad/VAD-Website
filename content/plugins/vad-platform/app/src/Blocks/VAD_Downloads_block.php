<?php

namespace Netdust\VAD\Blocks;

use Netdust\Traits\Cache;
use Netdust\Traits\Setters;
use Netdust\Traits\Templates;

class VAD_Downloads_block extends VAD_ACFBlock
{

    public function block_actions( ) {

        if( is_admin() ) {
            ?>
            <!-- wp:paragraph -->
            <p class="font-size:small;color:grey;">
                Klik hier om downloads toe te voegen of aan te vullen
            </p>
            <!-- /wp:paragraph -->
            <?php
        }
        else {
            $this->echo_template();
        }
    }

    public function echo_template( ) {

        echo $this->get_template($this->name, [
            'icon'=>$this->icon,
            'title'=>$this->title,
            'description'=>$this->description
        ]);

    }

}