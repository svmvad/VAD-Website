<?php

use Netdust\VAD\Pages\VAD_EditForm;

return function ($app) {


    // matches all the routes ending with an integer, routing to form
    \Netdust\Utils\Router\Router::virtual(
        '[*:trailing]/[:action]/[i:id]', $app->container()->get( VAD_EditForm::class ),'index'
    );

};