<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObjectInterface;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

// TODO in EventCalc oder so umbenennen
class RiseAndSetCalc
{
    private static function getStandardAltitude(string $objectClass)
    {
        // Meeus chapter 15
        switch ($objectClass) {
            case Sun::class:
                $h0 = -0.8333;
                break;
            case Moon::class:
                // TODO $h0 for the moon (Meeus 15.1)
                break;
            default:
                $h0 = -0.5667;
                break;
        }

        return $h0;
    }

    public static function getTransit(
        string $objectClass,
        Location $location,
        TimeOfInterest $toi
    ): float
    {
        /** @var AstronomicalObjectInterface $object */
        $object = new $objectClass();

        $h0 = self::getStandardAltitude($objectClass);
        $L = $location->getLongitudePositiveWest();

        $T0 = $toi->getApparentGreenwichMeanSiderealTime();

        // TODO Sun ...
        $jd0 = $toi->getJulianDay0();

        $T1 = TimeCalc::getJulianCenturiesFromJ2000($jd0 - 1);
        $rightAscension1 = SunCalc::getApparentRightAscension($T1);
        $declination1 = SunCalc::getApparentDeclination($T1);

        $T2 = TimeCalc::getJulianCenturiesFromJ2000($jd0);
        $rightAscension2 = SunCalc::getApparentRightAscension($T2);
        $declination2 = SunCalc::getApparentDeclination($T2);
        $d2Rad = deg2rad($declination2);

        $T3 = TimeCalc::getJulianCenturiesFromJ2000($jd0 + 1);
        $rightAscension3 = SunCalc::getApparentRightAscension($T3);
        $declination3 = SunCalc::getApparentDeclination($T3);

        // TODO
        $ra2 = 41.73129;
        $d2 = 18.44092;

        $coordinates = $object->getGeocentricEquatorialCoordinates();
        $ra2 = $coordinates->getRightAscension();
        $d2 = $coordinates->getDeclination();

        $h0Rad = deg2rad($h0);
        $d2Rad = deg2rad($d2);
        $latRad = $location->getLatitudeRad();

        // Meeus 15.1
        $cosH0 = (sin($h0Rad) - sin($latRad) * sin($d2Rad)) / (cos($latRad) * cos($d2Rad));
        $H0 = rad2deg(acos($cosH0));

        // Meeus 15.2
        $m0 = ($ra2 + $L - $T0) / 360; // Transit
        $m1 = $m0 - $H0 / 360; // Rise
        $m2 = $m0 + $H0 / 360; // Set

        $jd = $jd0 + $m0;
        $toi = new TimeOfInterest();
        $toi->setJulianDay($jd);

        var_dump($location, $toi->getDateTime()->format('Y-m-d H:i:s'));
    }

    public static function normalize(float $number): float
    {
        if ($number < 0) {
            $number += 1;
        }

        if ($number > 1) {
            $number -= 1;
        }

        return $number;
    }
}
