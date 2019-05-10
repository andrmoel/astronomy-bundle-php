<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Calculations\MoonCalc;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEclipticalSphericalCorrections;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEquatorialCorrections;
use Andrmoel\AstronomyBundle\Corrections\LocalHorizontalCorrections;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

// Berlin
$lat = 52.524;
$lon = 13.411;
$location = new Location($lat, $lon);

// Time of interest
$dateTime = new DateTime('2018-01-10 05:00:00');
$toi = new TimeOfInterest($dateTime);

// Create moon
$moon = new Moon($toi);

// Ecliptical spherical coordinates
$geoEclSphCoordinates = $moon->getGeocentricEclipticalSphericalCoordinates();

$correctionsEcl = new GeocentricEclipticalSphericalCorrections($toi);
$geoEclSphCoordinates = $correctionsEcl->correctCoordinates($geoEclSphCoordinates);

$eclLon = $geoEclSphCoordinates->getLongitude();
$eclLon = AngleUtil::dec2angle($eclLon);
$eclLat = $geoEclSphCoordinates->getLatitude();
$eclLat = AngleUtil::dec2angle($eclLat);
$radiusVector = $geoEclSphCoordinates->getRadiusVector();

// Equatorial coordinates
$geoEqaCoordinates = $moon->getGeocentricEquatorialCoordinates();

$corrections = new GeocentricEquatorialCorrections($toi);
$geoEqaCoordinates = $corrections->correctCoordinates($geoEqaCoordinates);

$rightAscension = $geoEqaCoordinates->getRightAscension();
$rightAscension = AngleUtil::dec2time($rightAscension);
$declination = $geoEqaCoordinates->getDeclination();
$declination = AngleUtil::dec2angle($declination);

// Local horizontal coordinates
$localHorizontalCoordinates = $moon->getLocalHorizontalCoordinates($location);

$corrections = new LocalHorizontalCorrections();
$localHorizontalCoordinates = $corrections->correctAtmosphericRefraction($localHorizontalCoordinates);

$azimuth = $localHorizontalCoordinates->getAzimuth(); // TODO Hier ist es der richtige Winkel...
$azimuth = AngleUtil::dec2angle(AngleUtil::normalizeAngle($azimuth));
$altitude = $localHorizontalCoordinates->getAltitude();
$altitude = AngleUtil::dec2angle($altitude);

$distance = MoonCalc::getDistanceToEarth($toi->getJulianCenturiesFromJ2000());
$isWaxingMoon = $moon->isWaxingMoon() ? 'yes' : 'no';
$illuminatedFraction = $moon->getIlluminatedFraction();
$positionAngleOfBrightLimb = $moon->getPositionAngleOfMoonsBrightLimb();

echo <<<END
+------------------------------------
| Moon
+------------------------------------
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')}
Observer's location: {$lat}°, {$lon}°

Ecliptical longitude: {$eclLon}
Ecliptical latitude: {$eclLat}
Right ascension: {$rightAscension}
Declination: {$declination}
Distance to earth: {$distance} km

Azimuth: {$azimuth} (apparent)
Altitude: {$altitude} (apparent)

Is waxing moon: {$isWaxingMoon}
Illuminated fraction: {$illuminatedFraction}
Position angle of bright limb: {$positionAngleOfBrightLimb}


END;
