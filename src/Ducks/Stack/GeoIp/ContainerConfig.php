<?php

namespace Ducks\Stack\GeoIp;

use Pimple;

class ContainerConfig
{

    public function process(Pimple $container)
    {
        $container['provider'] = $container->share(function($container) {
            return new \Geocoder\Provider\FreeGeoIpProvider($container['adapter']);
        });

        $container['adapter'] = $container->share(function($container) {
            return new \Geocoder\HttpAdapter\CurlHttpAdapter();
        });

        $container['header'] = 'X-Country';
    }

}

