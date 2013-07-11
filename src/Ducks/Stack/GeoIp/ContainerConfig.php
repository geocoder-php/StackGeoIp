<?php

namespace Ducks\Stack\GeoIp;

use Pimple;

class ContainerConfig
{

    public function process(Pimple $container)
    {
        $container['geocoder'] = $container->share(function($container) {
            $geocoder = new \Geocoder\Geocoder();
            $geocoder->registerProvider($container['provider']);

            return $geocoder;
        });

        $container['provider'] = $container->share(function($container) {
            return new \Geocoder\Provider\FreeGeoIpProvider($container['adapter']);
        });

        $container['adapter'] = $container->share(function($container) {
            return new \Geocoder\HttpAdapter\CurlHttpAdapter();
        });

        $container['header'] = 'X-Country';
    }

}

