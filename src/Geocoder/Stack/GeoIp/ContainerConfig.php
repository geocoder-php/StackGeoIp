<?php

/**
 * This file is part of the StackGeoIp package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Stack\GeoIp;

use Geocoder\Geocoder;
use Geocoder\HttpAdapter\CurlHttpAdapter;
use Geocoder\Provider\FreeGeoIpProvider;
use Pimple;

/**
 * @author Andy Leon <acleon@acleon.co.uk>
 */
class ContainerConfig
{
    public function process(Pimple $container)
    {
        $container['geocoder'] = $container->share(function ($container) {
            $geocoder = new Geocoder();
            $geocoder->registerProvider($container['provider']);

            return $geocoder;
        });

        $container['provider'] = $container->share(function ($container) {
            return new FreeGeoIpProvider($container['adapter']);
        });

        $container['adapter'] = $container->share(function ($container) {
            return new CurlHttpAdapter();
        });

        $container['header'] = 'X-Country';
    }
}
