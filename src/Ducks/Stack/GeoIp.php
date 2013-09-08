<?php

namespace Ducks\Stack;

use Ducks\Stack\GeoIp\ContainerConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class GeoIp implements HttpKernelInterface
{

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
