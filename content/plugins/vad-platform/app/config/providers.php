<?php

return [
    // Application
    '\Netdust\VAD\Providers\vad_Application',
    '\Netdust\VAD\Providers\vad_ApplicationAdmin',
    '\Netdust\VAD\Providers\vad_ApplicationFront',

    // Services
    // If the service is identified by an alias, give it as a second arg of an array
    ['\Netdust\VAD\Services\MagicLink\NTDST_MagicLink', "magiclink"],
    '\Netdust\VAD\Services\CustomLogin\NTDST_CustomLogin',
    ['\Netdust\VAD\Services\GeoSearch\VAD_GeoSearch', "geo_dir"],
    '\Netdust\VAD\Services\WooCommerce\VAD_WooCommerce'
];


