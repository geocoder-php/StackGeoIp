<?php

/**
 * This file is part of the StackGeoIp package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Stack;

use Geocoder\Stack\GeoIp\ContainerConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Andy Leon <acleon@acleon.co.uk>
 */
class GeoIp implements HttpKernelInterface
{
    private $app;
    private $container;

    public function __construct(HttpKernelInterface $app, array $options = [])
    {
        $this->app       = $app;
        $this->container = $this->setupContainer($options);
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if ($type !== HttpKernelInterface::MASTER_REQUEST) {
            return $this->app->handle($request, $type, $catch);
        }

        $geocoder = $this->container['geocoder'];
        $results  = $geocoder->geocode($request->getClientIp());

        if ($country = $results->getCountryCode()) {
            $request->headers->set($this->container['header'], $country, true);
        }

        return $this->app->handle($request, $type, $catch);
    }

    private function setupContainer(array $options)
    {
        $container = new \Pimple();

        $config = new ContainerConfig();
        $config->process($container);

        foreach ($options as $name => $value) {
            $container[$name] = $value;
        }

        return $container;
    }
}
