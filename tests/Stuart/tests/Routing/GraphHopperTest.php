<?php

namespace Stuart\Tests;


use Stuart\DropOff;
use Stuart\Pickup;
use Stuart\Routing\GraphHopper;

class GraphHopperTest extends \PHPUnit_Framework_TestCase
{

    private $graphHopper;

    public function setUp()
    {
        $this->graphHopper = new GraphHopper();
    }

    public function test()
    {
        // given
        $pickup = new Pickup();
        $pickup->setAddress('26 rue taine 75012 paris');
        $dropoff1 = new DropOff();
        $dropoff1
            ->setAddress('3 rue d edimbourg 75008 paris')
            ->setClientReference('ref1');
        $dropoff2 = new DropOff();
        $dropoff2
            ->setAddress('23 rue de richelieu 75002 paris')
            ->setClientReference('ref2');

        // when
        $result = $this->graphHopper->findRound($pickup, [$dropoff1, $dropoff2]);

        // then
        print_r($this->graphHopper->getRequest($pickup, [$dropoff1, $dropoff2]));
    }
}
