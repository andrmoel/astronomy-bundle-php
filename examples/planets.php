<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Jupiter;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Mars;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Mercury;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Neptune;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Planet;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Saturn;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Uranus;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

// Berlin
$location = new Location(52.524, 13.411);

// Time of interest
$toi = TimeOfInterest::createFromString('2019-05-24 22:00:00');

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
    $planetName = $tmp[count($tmp) - 1];

    // Get geocentric ecliptical coordinates
    $geoEclSphCoords = $planet->getGeocentricEclipticalSphericalCoordinates();

    $eclLatitude = $geoEclSphCoords->getLatitude();
    $eclLatitude = AngleUtil::dec2angle($eclLatitude);
    $eclLongitude = $geoEclSphCoords->getLongitude();
    $eclLongitude = AngleUtil::dec2angle($eclLongitude);

    // Get geocentric equatorial coordinates
    $geoEquSphCoords = $planet->getGeocentricEquatorialSphericalCoordinates();

    $rightAscension = $geoEquSphCoords->getRightAscension();
    $rightAscension = AngleUtil::dec2time($rightAscension);
    $declination = $geoEquSphCoords->getDeclination();
    $declination = AngleUtil::dec2angle($declination);

    // Get local horizontal coordinates
    $locHorCoords = $planet->getLocalHorizontalCoordinates($location);

    $azimuth = $locHorCoords->getAzimuth();
    $altitude = $locHorCoords->getAltitude();

    echo <<<END
+------------------------------------
| {$planetName}
+------------------------------------
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')}

Ecliptical longitude: {$eclLongitude}
Ecliptical latitude: {$eclLatitude}
Right ascending: {$rightAscension}
Declination: {$declination}

The sun seen from observer's location
Location: {$location->getLatitude()}°, {$location->getLongitude()}°
Azimuth: {$azimuth}
Altitude: {$altitude}

END;
}
