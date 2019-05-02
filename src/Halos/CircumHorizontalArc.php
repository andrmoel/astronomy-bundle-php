<?php

namespace Andrmoel\AstronomyBundle\Halos;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class CircumHorizontalArc
{
    public function getDaysOfVisiblity(int $year, Location $location): array
    {
        $visibleDays = [];

        $toi = new TimeOfInterest();

        $maxDays = TimeCalc::isLeapYear($year) ? 365 : 266;

        for ($dayOfYear = 0; $dayOfYear < $maxDays; $dayOfYear++) {
            $toi->setTimeByDayOfYear($year, $dayOfYear);
            $sun = new Sun($toi);

            $toiSolarNoon = $sun->getSolarNoon($location);

            $sun->setTimeOfInterest($toiSolarNoon);

            $altitude = $sun
                ->getLocalHorizontalCoordinates($location)
                ->getAltitude();

            if ($altitude >= 57.8) {
                $visibleDays[] = $toi->getDateTime()->format('Y-m-d');
            }
        }

        return $visibleDays;
    }
}