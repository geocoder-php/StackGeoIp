<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

require __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

$app->get('/', function(Request $request) {
    $ip = $request->getClientIp();

    $country = $request->headers->get('X-Country', 'UNKNOWN');

    return new Response($ip . ' => '. $country, 200);
});

$stack = (new Stack\Builder())
    ->push('Ducks\Stack\GeoIp')
;

$app = $stack->resolve($app);

$request = Request::createFromGlobals();
$response = $app->handle($request)->send();
$app->terminate($request, $response);

