<?php

namespace Stuart\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Stuart\DropOff;
use Stuart\Job;
use Stuart\Pickup;
use Stuart\Routing\GraphHopper;

class GraphHopperTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = array();
    }

    public function test_example()
    {
        // given
        $pickup = new Pickup();
        $pickup->setAddress('26 rue taine 75012 paris');

        // when
        $dropoffs = [
            $this->dropoff('23 rue de richelieu 75002 paris', '2018-05-30 12:40:00'),
            $this->dropoff('3 rue d\'edimbourg 75008 paris', '2018-05-30 12:45:00'),
            $this->dropoff('156 rue de charonne 75012 paris', '2018-05-30 12:30:00'),
            $this->dropoff('8 rue sidi brahim 75012 paris', '2018-05-30 14:30:00'),
            $this->dropoff('5 passage du chantier 75012 paris', '2018-05-30 12:30:00'),
            $this->dropoff('Hôpital Saint-Louis, 75010 Paris', '2018-05-30 13:20:00'),
            $this->dropoff('1 Rue des Deux Gares, 75010 Paris', '2018-05-30 12:30:00'),
            $this->dropoff('137 Rue la Fayette, 75010 Paris', '2018-05-30 12:30:00'),
            $this->dropoff('34 Rue Pierre Semard, 75009 Paris', '2018-05-30 12:00:00'),
            $this->dropoff('46 Rue Lecourbe, 75015 Paris', '2018-05-30 12:30:00'),
            $this->dropoff('178 Rue Lecourbe, 75015 Paris', '2018-05-30 13:00:00'),
            $this->dropoff('43 Rue des Alouettes 75019 Paris', '2018-05-30 12:30:00'),
            $this->dropoff('50 Rue Durantin, 75018 Paris', '2018-05-30 12:30:00'),
            $this->dropoff('47-33 Rue des Abbesses, 75018 Paris', '2018-05-30 13:30:00'),
            $this->dropoff('2 Boulevard de la Villette, 75019 Paris', '2018-05-30 14:30:00'),
            $this->dropoff('172 Rue de Charonne, 75011 Paris', '2018-05-30 15:30:00'),
            $this->dropoff('2-10 Passage Courtois, 75011 Paris', '2018-05-30 19:30:00'),
            $this->dropoff('23 Rue Servan, 75011 Paris', '2018-05-30 20:30:00'),
            $this->dropoff('71 Rue de la Fontaine au Roi, 75011 Paris', '2018-05-30 19:00:00'),
            $this->dropoff('37 Rue Albert Thomas 75010 Paris', '2018-05-30 20:45:00'),
            $this->dropoff('32-42 Rue du Faubourg Saint-Denis, 75010 Paris', '2018-05-30 19:30:00'),
            $this->dropoff('12 Rue d\'Uzès, 75002 Paris', '2018-05-30 20:39:00'),
            $this->dropoff('148 Rue de l\'Université, 75007 Paris', '2018-05-30 15:30:00'),
            $this->dropoff('64-66 Avenue d\'Iéna, 75116 Paris', '2018-05-30 18:30:00'),
            $this->dropoff('12 avenue claude vellefaux 75010 paris', '2018-05-30 19:00:00'),
            $this->dropoff('101 Avenue Victor Hugo, 75116 Paris', '2018-05-30 19:30:00')
        ];

        $config = array(
            'graphhopper_api_key' => 'd0198d64-e68e-4bbe-b3e8-88513f7301bb',
            'vehicle_count' => 10,
            'max_dropoffs' => 50,
            'slot_size_in_minutes' => 60,
            'max_distance' => 15000
        );

        $pricingStacking = 0;
        $graphHopper = new GraphHopper($config);
        $jobs = $graphHopper->findRounds($pickup, $dropoffs);
        foreach ($jobs as $job) {
            $job->setTransportType('bike');
            $res = $this->getPricing($job);
            $pricingStacking += $res->amount;
        }
        print_r('Total pricing with stacking is: ' . $pricingStacking . '. ');

        $pricingNoStacking = 0;
        foreach ($dropoffs as $dropoff) {
            $job = new Job();
            $job->setTransportType('bike');
            $job->pushPickup($pickup);
            $job->pushDropoff($dropoff);
            $res = $this->getPricing($job);
            $pricingNoStacking += $res->amount;
        }
        print_r('Total pricing without stacking is: ' . $pricingNoStacking);
    }

    private function getPricing($job)
    {
        $environment = \Stuart\Infrastructure\Environment::SANDBOX;
        $api_client_id = 'c6058849d0a056fc743203acb8e6a850dad103485c3edc51b16a9260cc7a7688'; // can be found here: https://admin-sandbox.stuart.com/client/api
        $api_client_secret = 'aa6a415fce31967501662c1960fcbfbf4745acff99acb19dbc1aae6f76c9c619'; // can be found here: https://admin-sandbox.stuart.com/client/api
        $authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret);

        $httpClient = new \Stuart\Infrastructure\HttpClient($authenticator);
        $client = new \Stuart\Client($httpClient);

        return $client->getPricing($job);
    }

    private function dropoff($address, $dropoffAtAsText)
    {
        $dropoff = new DropOff();
        $dropoff->setAddress($address)
            ->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', $dropoffAtAsText));
        return $dropoff;
    }

    public function test_returns_vehicles()
    {
        // given
        $locationId = 'some-location-id';
        $lat = 'some-lat';
        $lon = 'some-lon';
        $vehicleCount = 1;
        $maxDropoffs = 5;
        $maxDistance = 1;

        // when
        $result = GraphHopper\Formatter::convertToVehicles($locationId, $lat, $lon, $vehicleCount, $maxDropoffs, $maxDistance);

        // then
        self::assertEquals(
            array(
                0 => array(
                    'vehicle_id' => '0001',
                    'start_address' => array(
                        'location_id' => $locationId,
                        'lat' => $lat,
                        'lon' => $lon
                    ),
                    'return_to_depot' => false,
                    'max_activities' => $maxDropoffs,
                    'max_distance' => $maxDistance
                )
            )
            , $result);
    }

    public function test_returns_address()
    {
        // given
        $locationId = 'some-location-id';
        $lat = 'some-lat';
        $lon = 'some-lon';

        // when
        $result = GraphHopper\Formatter::convertToAddress($locationId, $lat, $lon);

        // then
        self::assertEquals(
            array(
                'location_id' => $locationId,
                'lat' => $lat,
                'lon' => $lon
            )
            , $result);
    }

    public function test_build_optimize_query()
    {
        // given
        $config = array(
            'graphhopper_api_key' => 'api-key',
            'vehicle_count' => 1,
            'max_dropoffs' => 50,
            'slot_size_in_minutes' => 60,
            'max_distance' => 1
        );
        $clientMock = \Phake::mock(GraphHopper\Client::class);

        $pickup = new Pickup();
        $pickup->setAddress('some-pickup-address');

        $dropoff = new DropOff();
        $dropoff->setAddress('some-dropoff-address')
            ->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-30 20:45:00'));

        \Phake::when($clientMock)->geocode->thenReturn(
            (object)array('lat' => 'lat', 'lon' => 'lon')
        );
        \Phake::when($clientMock)->buildOptimizeQuery->thenCallParent();

        // when
        $query = $clientMock->buildOptimizeQuery($pickup, array($dropoff), $config);

        // then
        self::assertEquals(
            array(
                'vehicles' => array(
                    0 => array(
                        'vehicle_id' => '0001',
                        'start_address' => array(
                            'location_id' => 'some-pickup-address',
                            'lat' => 'lat',
                            'lon' => 'lon'
                        ),
                        'return_to_depot' => false,
                        'max_activities' => 50,
                        'max_distance' => 1
                    )
                ),
                'services' => array(
                    0 => array(
                        'id' => 'some-pickup-address',
                        'address' => array(
                            'location_id' => 'some-pickup-address',
                            'lat' => 'lat',
                            'lon' => 'lon'
                        )
                    ),
                    1 => array(
                        'id' => 'some-dropoff-address',
                        'address' => array(
                            'location_id' => 'some-dropoff-address',
                            'lat' => 'lat',
                            'lon' => 'lon'
                        ),
                        'time_windows' => array(
                            0 => array(
                                'earliest' => 1527713100,
                                'latest' => 1527716700
                            )
                        )
                    )
                )
            )
            , $query);
    }

    public function test_call_optimize_api_with_correct_parameters()
    {
        // given
        $config = array(
            'graphhopper_api_key' => 'd0198d64-e68e-4bbe-b3e8-88513f7301bb',
            'vehicle_count' => 10,
            'max_dropoffs' => 50,
            'slot_size_in_minutes' => 60,
            'max_distance' => 15000
        );
        $client = $this->guzzleMock();
        $clientMock = \Phake::partialMock(GraphHopper\Client::class, $config['graphhopper_api_key'], $client);
        \Phake::when($clientMock)->geocode->thenReturn(
            (object)array('lat' => 'lat', 'lon' => 'lon')
        );
        \Phake::when($clientMock)->buildOptimizeQuery->thenReturn(
            (object)array('some' => 'result')
        );
        \Phake::when($clientMock)->optimize->thenCallParent();
        $pickup = new Pickup();
        $pickup->setAddress('some-pickup-address');

        $dropoff = new DropOff();
        $dropoff->setAddress('some-dropoff-address')
            ->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-30 20:45:00'));

        // when
        $clientMock->optimize($pickup, $dropoff, $config);

        // then
        $transaction = $this->container[0];
        self::assertEquals('POST', $transaction['request']->getMethod());
        self::assertEquals('/api/1/vrp/optimize', $transaction['request']->getUri()->getPath());
        self::assertEquals('graphhopper.com', $transaction['request']->getUri()->getHost());
        self::assertEquals('{"some":"result"}', (string)$transaction['request']->getBody());
        self::assertEquals('key=d0198d64-e68e-4bbe-b3e8-88513f7301bb', $transaction['request']->getUri()->getQuery());
    }

    private function guzzleMock()
    {
        $history = Middleware::history($this->container);
        $mock = new MockHandler([
            new Response(200, [], "")
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        return new Client(['handler' => $handler]);
    }
}
