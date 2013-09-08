StackGeoIp
==========

Geolocation [Stack](http://stackphp.com/) middleware that adds geolocation
results to the request for subsequent middlewares by leveraging the
[Geocoder](http://geocoder-php.org/) library.

[![Build
Status](https://travis-ci.org/geocoder-php/StackGeoIp.png)](https://travis-ci.org/geocoder-php/StackGeoIp)


Usage
-----

### Example

Here we create a simple application that returns the IP address of the
request and the contents of the `X-Country` header to the browser. Normally,
the `X-Country` header would not exists.

By wrapping the GeoIP middleware around it using the StackBuilder, if the
IP address can be matched to a country, the `X-Country` header will be set
to the two-letter code for that country, and be available to the application.

```php
<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

require __DIR__ . '/vendor/autoload.php';

$app = new \Silex\Application();

$app->get('/', function(Request $request) {
    $ip      = $request->getClientIp();
    $country = $request->headers->get('X-Country', 'UNKNOWN');

    return new Response($ip . ' => '. $country, 200);
});

$stack = (new \Stack\Builder())
    ->push('Ducks\Stack\GeoIp')
    ;

$app = $stack->resolve($app);

$request  = Request::createFromGlobals();
$response = $app->handle($request)->send();
$app->terminate($request, $response);
```


### Options

The following options can be used:

* **adapter** (optional): The Geocoder HTTP adapter to use. Defaults
  to cURL adapter.

* **provider** (optional): The Geocoder provider to use. Defaults
  to the FreeGeoIP provider. For production, it is recommended to
  use a provider that relies on local files rather than HTTP
  requests, such as Max Mind binary provider.

* **header** (optional): The name of the HTTP header to store
  the country result in. Defaults to "X-Country".

See [the Geocoder documentation](http://geocoder-php.org/Geocoder/) for a list
of available adapters and providers.


License
-------

StackGeoIp is released under the MIT License. See the bundled LICENSE file for
details.
