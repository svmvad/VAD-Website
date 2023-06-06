<?php


return function ($app) {

    \Netdust\Utils\AutoLoader::setup_autoloader( [
        'Netdust\VAD\\'=> $app->dir().'/app/src/',
        'Netdust\VAD\Services\\'=> $app->dir().'/services/'
    ] );

};