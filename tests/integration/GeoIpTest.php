<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GeoIpTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $provider = $this->getMockBuilder('Geocoder\Provider\FreeGeoIpProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $provider->expects($this->any())
            ->method('setMaxResults')
            ->will($this->returnValue($provider))
            ;

        $provider->expects($this->any())
            ->method('getGeocodedData')
            ->will($this->returnValueMap(array(
                array('127.0.0.1',          array()),
                array('202.158.214.106',    array('countryCode' => 'AU')),
                array('70.38.0.135',        array('countryCode' => 'CA')),
                array('83.142.228.128',     array('countryCode' => 'GB')),
                array('128.30.2.36',        array('countryCode' => 'US')),
            )))
            ;

        $app = new \Stack\CallableHttpKernel(function(Request $request) {
            return new Response($request->headers->get('X-Country', 'NONE'));
        });

        $stack = new \Stack\Builder();
        $stack->push('Geocoder\Stack\GeoIp', array('provider' => $provider));

        $this->app = $stack->resolve($app);
    }

    public function testWithoutGeolocation()
    {
        $request  = Request::create('/');
        $response = $this->app->handle($request);

        $this->assertEquals('NONE', $response->getContent());
    }

    /**
     * @dataProvider dataProviderForTestWithGeolocation
     */
    public function testWithGeolocation($ip, $expectedCountry)
    {
        $request = Request::create('/');
        $request->server->set('REMOTE_ADDR', $ip);

        $response = $this->app->handle($request);

        $this->assertEquals($expectedCountry, $response->getContent());
    }

    public function dataProviderForTestWithGeolocation()
    {
        return array(
            array('202.158.214.106',    'AU'),
            array('70.38.0.135',        'CA'),
            array('83.142.228.128',     'GB'),
            array('128.30.2.36',        'US'),
        );
    }

    public function getDefaults()
    {
        return array(
            'latitude'      => null,
            'longitude'     => null,
            'bounds'        => null,
            'streetNumber'  => null,
            'streetName'    => null,
            'city'          => null,
            'zipcode'       => null,
            'cityDistrict'  => null,
            'county'        => null,
            'countyCode'    => null,
            'region'        => null,
            'regionCode'    => null,
            'country'       => null,
            'countryCode'   => null,
            'timezone'      => null,
        );
    }
}
