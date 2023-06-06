<?php

namespace Netdust\VAD\Blocks;

class VAD_RelatedArticlesSection_block extends VAD_Section_block
{
    public function block_actions( ) {

        if( is_admin() ) {
            ?>
            <!-- wp:paragraph -->
            <h4>
                <?php
                echo 'related artcile group';
                ?>
            </h4>
            <!-- /wp:paragraph -->
            <?php
        }
        else {
            $this->echo_template();
        }
    }

    public function echo_template( ) {
        echo $this->get_template($this->name);
    }

}