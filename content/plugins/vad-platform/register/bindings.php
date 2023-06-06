<?php

use Netdust\Core\Request;
use Netdust\Utils\Logger\Logger;
use Netdust\Utils\Logger\LoggerInterface;
use Netdust\Utils\Logger\SimpleLogger;
use Netdust\Utils\Router\Router;
use Netdust\Utils\Router\SimpleRouter;
use Netdust\Utils\Router\RouterInterface;
use Netdust\VAD\Pages\VAD_EditForm;

return function ($app) {

    //bind router
    $app->container()->bind( RouterInterface::class, new SimpleRouter() );
    Router::setRouter( $app->container()->get(RouterInterface::class) );

    //bind logger
    $app->container()->bind( LoggerInterface::class, SimpleLogger::class );
    Logger::setLogger( $app->container()->get(LoggerInterface::class) );

    //bind editform
    /*
    $app->container()->bind( VAD_EditForm::class, function( $container ) use ( $app ) {

        $request = $container->get( Request::class );
        if( str_contains($request->getPath(), 'doorverwijsgids' ) ) {
            return new VAD_EditForm($app->config('edit-doorverwijs'), $app->tpl_dir() );
        }

        if( str_contains($request->getPath(), 'onderzoeksdatabank' ) ) {
            return new VAD_EditForm($app->config('edit-research'), $app->tpl_dir() );
        }

        return null;

    } ); */

};