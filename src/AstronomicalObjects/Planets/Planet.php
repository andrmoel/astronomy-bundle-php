<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObject;
use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\EarthRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\VSOP87Interface;
use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEclipticalSphericalCorrections;
use Andrmoel\AstronomyBundle\Corrections\LocalHorizontalCorrections;
use Andrmoel\AstronomyBundle\Events\RiseSetTransit\RiseSetTransit;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use Andrmoel\AstronomyBundle\Utils\DistanceUtil;

abstract class Planet extends AstronomicalObject implements PlanetInterface
{
    /** @var VSOP87Interface */
    protected $VSOP87_SPHERICAL;

    /** @var VSOP87Interface */
    protected $VSOP87_RECTANGULAR;

    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        $t = $this->toi->getJulianMillenniaFromJ2000();
        $coefficients = VSOP87Calc::solve($this->VSOP87_RECTANGULAR, $t);

        $x = $coefficients[0];
        $y = $coefficients[1];
        $z = $coefficients[2];

        return new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
    }

    public function getHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates
    {
        $t = $this->toi->getJulianMillenniaFromJ2000();
        $coefficients = VSOP87Calc::solve($this->VSOP87_SPHERICAL, $t);

        $L = $coefficients[0];
        $B = $coefficients[1];
        $R = $coefficients[2];

        $L = AngleUtil::normalizeAngle(rad2deg($L));
        $B = rad2deg($B);

        return new HeliocentricEclipticalSphericalCoordinates($B, $L, $R);
    }

    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        $T = $this->toi->getJulianCenturiesFromJ2000();
        $t = $this->toi->getJulianMillenniaFromJ2000();
        $JD = $this->toi->getJulianDay();

        $coefficientsEarth = VSOP87Calc::solve(EarthRectangularVSOP87::class, $t);

        // Meeus 33 - Light time corrections
        for ($i = 0; $i < 2; $i++) {
            $t = TimeCalc::julianDay2julianMillenniaJ2000($JD);

            $coefficients = VSOP87Calc::solve($this->VSOP87_RECTANGULAR, $t);

            // Get geocentric coordinates
            $X = $coefficients[0] - $coefficientsEarth[0];
            $Y = $coefficients[1] - $coefficientsEarth[1];
            $Z = $coefficients[2] - $coefficientsEarth[2];

            $d = sqrt(pow($X, 2) + pow($Y, 2) + pow($Z, 2));
            $tau = 0.0057755183 * $d;

            $JD -= $tau;
        }

        $geoEclRecCoord = new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
        $geoEclSphCoord = $geoEclRecCoord->getGeocentricEclipticalSphericalCoordinates();

        // Meeus 33 - Aberration correction
        $geoEclSphCoord = GeocentricEclipticalSphericalCorrections::correctEffectOfAberration($geoEclSphCoord, $T);

        // Meeus 33 - Nutation correction
        $geoEclSphCoord = GeocentricEclipticalSphericalCorrections::correctEffectOfNutation($geoEclSphCoord, $T);

        return $geoEclSphCoord;
    }

    // TODO test it!
    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        return $this->getGeocentricEclipticalSphericalCoordinates()
            ->getGeocentricEquatorialRectangularCoordinates($this->T);
    }

    public function getGeocentricEquatorialSphericalCoordinates(): GeocentricEquatorialSphericalCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates()
            ->getGeocentricEquatorialSphericalCoordinates($this->T);
    }

    public function getLocalHorizontalCoordinates(Location $location, bool $refraction = true): LocalHorizontalCoordinates
    {
        $locHorCoord = $this
            ->getGeocentricEquatorialSphericalCoordinates()
            ->getLocalHorizontalCoordinates($location, $this->T);

        if ($refraction) {
            $locHorCoord = LocalHorizontalCorrections::correctAtmosphericRefraction($locHorCoord);
        }

        return $locHorCoord;
    }

    public function getRise(Location $location): TimeOfInterest
    {
        $ras = new RiseSetTransit(get_class($this), $location, $this->toi);
        return $ras->getRise();
    }

    public function getUpperCulmination(Location $location): TimeOfInterest
    {
        $ras = new RiseSetTransit(get_class($this), $location, $this->toi);
        return $ras->getTransit();
    }

    public function getSet(Location $location): TimeOfInterest
    {
        $ras = new RiseSetTransit(get_class($this), $location, $this->toi);
        return $ras->getSet();
    }

    public function getDistanceToEarthInAu(): float
    {
        $geoEclRecCoord = $this->getGeocentricEquatorialRectangularCoordinates();

        $x = $geoEclRecCoord->getX();
        $y = $geoEclRecCoord->getY();
        $z = $geoEclRecCoord->getZ();

        $d = sqrt($x * $x + $y * $y + $z * $z);

        return $d;
    }

    public function getDistanceToEarthInKm(): float
    {
        $d = $this->getDistanceToEarthInAu();

        return DistanceUtil::au2km($d);
    }
}
