<?php

namespace Stuart\Tests;

use Stuart\DropOff;
use Stuart\Pickup;
use Stuart\Routing\GraphHopper;

class GraphHopperTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        print_r('setup');
    }

    public function test_bla_bla()
    {
        // given
        $pickup = new Pickup();
        $pickup->setAddress('26 rue taine 75012 paris');

        // when
        $dropoffs = [
            $this->dropoff('23 rue de richelieu 75002 paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 12:40:00')),
            $this->dropoff('3 rue d\'edimbourg 75008 paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 12:45:00')),
            $this->dropoff('156 rue de charonne 75012 paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 12:30:00')),
            $this->dropoff('8 rue sidi brahim 75012 paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 14:30:00')),
            $this->dropoff('5 passage du chantier 75012 paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 12:30:00')),
            $this->dropoff('Hôpital Saint-Louis, 75010 Paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 13:20:00')),
            $this->dropoff('1 Rue des Deux Gares, 75010 Paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 12:30:00')),
            $this->dropoff('137 Rue la Fayette, 75010 Paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 12:30:00')),
            $this->dropoff('34 Rue Pierre Semard, 75009 Paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 12:00:00')),
            $this->dropoff('46 Rue Lecourbe, 75015 Paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 12:30:00')),
            $this->dropoff('178 Rue Lecourbe, 75015 Paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 13:00:00')),
            $this->dropoff('43 Rue des Alouettes 75019 Paris', \DateTime::createFromFormat('Y-m-d H:i:s', '2018-05-29 12:30:00'))
        ];

        $config = array(
            'vehicle_count' => 1,
            'return_trip' => false,
            'max_dropoffs' => 8,
            'slot_size_in_minutes' => 30
        );

        $graphHopper = new GraphHopper($pickup, $dropoffs, $config);

        $result = $graphHopper->findRounds();

        // then
        foreach ($result->jobs as $job) {
            $job->setTransportType('car');
            $res = $this->createJob($job);
        }
    }

    private function createJob($job)
    {
        $environment = \Stuart\Infrastructure\Environment::SANDBOX;
        $api_client_id = '65176d7a1f4e734f6a4d737190825f166f8dadf69fb40af52fffdeac4593e4bc'; // can be found here: https://admin-sandbox.stuart.com/client/api
        $api_client_secret = '681ae68635c7aadef5cd82cbeeef357a808cd9dc794811296446f19268d48fcd'; // can be found here: https://admin-sandbox.stuart.com/client/api
        $authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret);

        $httpClient = new \Stuart\Infrastructure\HttpClient($authenticator);
        $client = new \Stuart\Client($httpClient);

        return $client->createJob($job);
    }

    private function dropoff($address, $dropoffAt)
    {
        $dropoff = new DropOff();
        $dropoff->setAddress($address)
            ->setDropoffAt($dropoffAt);
        return $dropoff;
    }
}
