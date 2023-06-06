<?php

namespace Netdust\VAD\Blocks;

class VAD_ACFBlock extends \Netdust\Service\Blocks\ACFBlock
{

    public function __construct(array $args = [])
    {
        parent::__construct($args);
        $this->name = $this->name ?? 'block_item';
        $this->template_root = $this->template_root ?? dirname(__DIR__, 2).'/templates';
    }

}