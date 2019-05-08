<?php

include __DIR__ . '/../vendor/autoload.php';

use \Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Planet;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Mercury;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Mars;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Jupiter;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Saturn;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Uranus;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Neptune;
use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEquatorialCorrections;
use Andrmoel\AstronomyBundle\Corrections\LocalHorizontalCorrections;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

// Berlin
$location = new Location(52.524, 13.411);

// Time of interest
$dateTime = new DateTime('2018-05-13 20:00:00');
$toi = new TimeOfInterest($dateTime);

$T = $toi->getJulianCenturiesFromJ2000();
$e = EarthCalc::getTrueObliquityOfEcliptic($T);

$planets = array(
    new Mercury($toi),
    new Venus($toi),
    new Mars($toi),
    new Jupiter($toi),
    new Saturn($toi),
    new Uranus($toi),
    new Neptune($toi),
);

/** @var Planet $planet */
foreach ($planets as $planet) {
    // Create planet
    $planet = new $planet($toi);

    // Get planet name from class
    $planetName = get_class($planet);
    $tmp = explode('/', $planetName);
    $planetName = $tmp[count($tmp) -1];

    // Get geocentric ecliptical coordinates
    $geoEclSphCoordinates = $planet
        ->getHeliocentricEclipticalSphericalCoordinates()
        ->getGeocentricEclipticalSphericalCoordinates($toi);

    $eclLatitude = $geoEclSphCoordinates->getLatitude();
    $eclLatitude = AngleUtil::dec2angle($eclLatitude);
    $eclLongitude = $geoEclSphCoordinates->getLongitude();
    $eclLongitude = AngleUtil::dec2angle($eclLongitude);

    // Get geocentric equatorial coordinates
    $geoEquCorodinates = $geoEclSphCoordinates
        ->getGeocentricEquatorialCoordinates($toi);

    $corrections = new GeocentricEquatorialCorrections($toi);
    $geoEquCorodinates = $corrections->correctCoordinates($geoEquCorodinates);

    $rightAscension = $geoEquCorodinates->getRightAscension();
    $rightAscension = AngleUtil::dec2time($rightAscension);
    $declination = $geoEquCorodinates->getDeclination();
    $declination = AngleUtil::dec2angle($declination);

    // Get local horizontal coordinates
    $localHorizontalCoordinates = $geoEquCorodinates->getLocalHorizontalCoordinates($location, $toi);

    $corrections = new LocalHorizontalCorrections();
    $localHorizontalCoordinates = $corrections->correctAtmosphericRefraction($localHorizontalCoordinates);

    $azimuth = $localHorizontalCoordinates->getAzimuth() + 180; // TODO FALSCHER WERT. Laut Stellarium 294.45... Und was wenn > 360°???
    $azimuth = AngleUtil::dec2angle(AngleUtil::normalizeAngle($azimuth));
    $altitude = $localHorizontalCoordinates->getAltitude();
    $altitude = AngleUtil::dec2angle($altitude);

    echo <<<END
+------------------------------------
| {$planetName}
+------------------------------------
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')}
Observer's location: {$lat}°, {$lon}°

Ecliptical longitude: {$eclLongitude}
Ecliptical latitude: {$eclLatitude}
Right ascending: {$rightAscension}
Declination: {$declination}
Azimuth: {$azimuth} (apparent)
Altitude: {$altitude} (apparent)


END;
}
