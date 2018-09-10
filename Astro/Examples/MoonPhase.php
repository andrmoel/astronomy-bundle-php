<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 01.06.15
 * Time: 20:22
 */

namespace App\Util\Astro\Examples;

use App\Util\Astro\AstronomicalObjects\Earth;
use App\Util\Astro\AstronomicalObjects\Moon;
use App\Util\Astro\AstronomicalObjects\Sun;
use App\Util\Astro\TimeOfInterest;

class MoonPhase
{
    /**
     * Run example
     */
    public function run()
    {
        // Berlin
        $lat = 52.518611;
        $lon = 13.408333;

        // Time of interest is today
        $toi = new TimeOfInterest();
        $time = strtotime('2000-01-01 00:00');
        $toi->setUnixTime($time);
        var_dump($toi->getTimeString());

        $moon = new Moon();

        for($i = 0; $i < 70; $i++) {
            $toi->setUnixTime($time + 60 * 60 * 12 * $i);
            $moon->setTimeOfInterest($toi);

            $phase = $moon->getIlluminatedFraction();
            $positionAngle = $moon->getPositionAngleOfMoonsBrightLimb();

            echo "<hr>" . $toi->getTimeString();
            var_dump($phase, $positionAngle);
        }


        die();
    }
}
