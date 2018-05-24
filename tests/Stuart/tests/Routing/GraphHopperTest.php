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
            ->setAddress('3 rue d\'edimbourg 75008 paris')
            ->setClientReference('ref1');

        $dropoff2 = new DropOff();
        $dropoff2
            ->setAddress('23 rue de richelieu 75002 paris')
            ->setClientReference('ref2');

        $dropoff3 = new DropOff();
        $dropoff3
            ->setAddress('156 rue de charonne 75012 paris')
            ->setClientReference('ref3');

        $dropoff4 = new DropOff();
        $dropoff4
            ->setAddress('8 rue sidi brahim 75012 paris')
            ->setClientReference('ref4');

        $dropoff5 = new DropOff();
        $dropoff5
            ->setAddress('5 passage du chantier 75012 paris')
            ->setClientReference('ref5');


        $dropoff6 = new DropOff();
        $dropoff6
            ->setAddress('Hôpital Saint-Louis, 75010 Paris')
            ->setClientReference('ref6');


        // when
        $result = $this->graphHopper->findRound($pickup,
            [
                $this->dropoff('23 rue de richelieu 75002 paris'), $this->dropoff('3 rue d\'edimbourg 75008 paris'), $this->dropoff('156 rue de charonne 75012 paris'),
                $this->dropoff('8 rue sidi brahim 75012 paris'), $this->dropoff('5 passage du chantier 75012 paris'), $this->dropoff('Hôpital Saint-Louis, 75010 Paris'),
                $this->dropoff('1 Rue des Deux Gares, 75010 Paris'),  $this->dropoff('137 Rue la Fayette, 75010 Paris'), $this->dropoff('34 Rue Pierre Semard, 75009 Paris'),
                $this->dropoff('46 Rue Lecourbe, 75015 Paris'),  $this->dropoff('178 Rue Lecourbe, 75015 Paris'), $this->dropoff('43 Rue des Alouettes 75019 Paris')
            ]);

        // then

    }

    private function dropoff($address)
    {
        $dropoff = new DropOff();
        $dropoff
            ->setAddress($address);
        return $dropoff;
    }
}
