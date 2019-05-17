<?php

namespace Andrmoel\AstronomyBundle\Events\RiseSetTransit;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObjectInterface;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Calculations\MoonCalc;
use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use Andrmoel\AstronomyBundle\Utils\InterpolationUtil;

class RiseSetTransit
{
    const EVENT_TYPE_RISE = 'rise';
    const EVENT_TYPE_TRANSIT = 'transit';
    const EVENT_TYPE_SET = 'set';

    private $astronomicalObjectClass;
    /** @var AstronomicalObjectInterface */
    private $astronomicalObject;
    private $location;
    private $toi;

    public function __construct(string $astronomicalObjectClass, Location $location, TimeOfInterest $toi)
    {
        $toiJD0 = new TimeOfInterest();
        $toiJD0->setJulianDay($toi->getJulianDay0());

        /** @var AstronomicalObjectInterface $astronomicalObject */
        $astronomicalObject = new $astronomicalObjectClass();
        $astronomicalObject->setTimeOfInterest($toiJD0);

        $this->astronomicalObjectClass = $astronomicalObjectClass;
        $this->astronomicalObject = $astronomicalObject;
        $this->location = $location;
        $this->toi = $toiJD0;

    }

    public function getRise(): ?TimeOfInterest
    {
        $jd0 = $this->toi->getJulianDay0();
        $m = $this->getApproximatedM(self::EVENT_TYPE_RISE);
        $dm = $this->getCorrectionForM(self::EVENT_TYPE_RISE);

        if (is_nan($m)) {
            return null;
        }

        $toi = TimeOfInterest::createFromJulianDay($jd0 + $m + $dm);

        return $toi;
    }

    public function getTransit(): TimeOfInterest
    {
        $jd0 = $this->toi->getJulianDay0();
        $m = $this->getApproximatedM(self::EVENT_TYPE_TRANSIT);
        $dm = $this->getCorrectionForM(self::EVENT_TYPE_TRANSIT);

        $toi = TimeOfInterest::createFromJulianDay($jd0 + $m + $dm);

        return $toi;
    }

    public function getSet(): TimeOfInterest
    {
        $jd0 = $this->toi->getJulianDay0();
        $m = $this->getApproximatedM(self::EVENT_TYPE_SET);
        $dm = $this->getCorrectionForM(self::EVENT_TYPE_SET);

        if (is_nan($m)) {
            return null;
        }

        $toi = TimeOfInterest::createFromJulianDay($jd0 + $m + $dm);

        return $toi;
    }

    private function getCorrectionForM(string $eventType): float
    {
        $coord1 = $this->getGeocentricEquatorialCoordinatesOfAstronomicalObject(-1);
        $coord2 = $this->getGeocentricEquatorialCoordinatesOfAstronomicalObject(0);
        $coord3 = $this->getGeocentricEquatorialCoordinatesOfAstronomicalObject(1);

        $ra1 = $coord1->getRightAscension();
        $d1 = $coord1->getDeclination();
        $ra2 = $coord2->getRightAscension();
        $d2 = $coord2->getDeclination();
        $ra3 = $coord3->getRightAscension();
        $d3 = $coord3->getDeclination();

        $m = $this->getApproximatedM($eventType);

        $dT = TimeCalc::getDeltaT(
            $this->toi->getYear(),
            $this->toi->getMonth()
        );
        $n = $m + $dT / 86400;

        $ra = InterpolationUtil::interpolate($ra1, $ra2, $ra3, $m, $n);

        $T0 = $this->toi->getApparentGreenwichMeanSiderealTime();
        $L = $this->location->getLongitudePositiveWest();

        $t0 = $T0 + 360.985647 * $m;
        $t0 = AngleUtil::normalizeAngle($t0);

        // TODO Auslagern, Siehe Meeus p 92
        $H = $t0 - $L - $ra;

        if ($eventType === self::EVENT_TYPE_TRANSIT) {
            $dm = $H / -360;
        } else {
            $lat = $this->location->getLatitude();
            $d = InterpolationUtil::interpolate($d1, $d2, $d3, $m, $n);

            $latRad = deg2rad($lat);
            $dRad = deg2rad($d);
            $HRad = deg2rad($H);

            $h = sin($latRad) * sin($dRad) + cos($latRad) * cos($dRad) * cos($HRad);
            $h = rad2deg(asin($h));
            $h0 = $this->getStandardAltitude();

            $dm = ($h - $h0) / (360 * cos($dRad) * cos($latRad) * sin($HRad));
        }

        return $dm;
    }

    private function getApproximatedM(string $eventType): float
    {
        $coordinates = $this->getGeocentricEquatorialCoordinatesOfAstronomicalObject(0);
        $ra = $coordinates->getRightAscension();
        $d = $coordinates->getDeclination();

        $h0 = self::getStandardAltitude();
        $T0 = $this->toi->getApparentGreenwichMeanSiderealTime();
        $L = $this->location->getLongitudePositiveWest();
        $lat = $this->location->getLatitude();

        $h0Rad = deg2rad($h0);
        $dRad = deg2rad($d);
        $latRad = deg2rad($lat);

        // Meeus 15.1
        $cosH0 = (sin($h0Rad) - sin($latRad) * sin($dRad)) / (cos($latRad) * cos($dRad));
        $H0 = rad2deg(acos($cosH0));

        // Meeus 15.2
        $m0 = ($ra + $L - $T0) / 360; // Transit

        if ($m0 < 0) {
            $m0 += 1;
        }

        if ($m0 > 1) {
            $m0 -= 1;
        }

        switch ($eventType) {
            case self::EVENT_TYPE_TRANSIT:
                $m = $m0;
                break;
            case self::EVENT_TYPE_RISE:
                $m = $m0 - $H0 / 360;
                break;
            case self::EVENT_TYPE_SET:
                $m = $m0 + $H0 / 360;
                break;
        }

        if ($m < 0) {
            $m += 1;
        }

        if ($m > 1) {
            $m -= 1;
        }

        // TODO $m must be between 0 and 1 ?

        return $m;
    }

    private function getStandardAltitude()
    {
        // Meeus chapter 15
        switch ($this->astronomicalObjectClass) {
            case Sun::class:
                $h0 = -0.8333;
                break;
            case Moon::class:
                $T = 0; // TODO $h0 for the moon (Meeus 15.1)
                $T = $this->astronomicalObject->getTimeOfInterest()->getJulianCenturiesFromJ2000();
                $pi = MoonCalc::getEquatorialHorizontalParallax($T);
                $h0 = 0.7275 * $pi * 0.5667;
                break;
            default:
                $h0 = -0.5667;
                break;
        }

        return $h0;
    }

    private function getGeocentricEquatorialCoordinatesOfAstronomicalObject(
        int $diff = 0
    ): GeocentricEquatorialSphericalCoordinates {
        $jd0 = $this->toi->getJulianDay0();
        $toi = TimeOfInterest::createFromJulianDay($jd0 + $diff);

        $astronomicalObject = clone $this->astronomicalObject;
        $astronomicalObject->setTimeOfInterest($toi);

        return $astronomicalObject->getGeocentricEquatorialSphericalCoordinates();
    }
}
