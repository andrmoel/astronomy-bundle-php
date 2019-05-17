# Introduction

!!! ATTENTION !!!!!!!!!!!!!!!!!!! 

This bundle on Version 0.X.X is still in development. Use on your own risk.
A stable release will be provided with version 1.X.X.

!!! ATTENTION !!!!!!!!!!!!!!!!!!!

# Installation

Use composer to install this package.

```console
composer require andrmoel/astronomy-bundle
```

# Usage

## Time of Interest

The time of interest (TOI) object represents the time for which all of the astronomical calculations are done.
E.g. If you want to calculate the position of the sun for July 02nd 2017 at 12:00:00 UTC, you need to initialize
the TOI as follow.

```php
$dateTime = new \DateTime('2017-07-02 12:00:00');
$toi = new TimeOfInterest($dateTime);
```

The TOI objects provides all methods which are needed for astronomical calculations:
* Get Julian Day
* Get Jualian Centuries from J2000
* Get Julian Millennia from J2000

## Location

The location object represents the location of the observer on the earth's surface.

```php
// Initialize Location object for Berlin
$location = new Location(52.524, 13.411);

// Initialize Location with elevation (Mt. Everest)
$location = new Location(27.98787, 86.92483, 8848);
```

### Coordinate Systems

The bundle provides the common astronomical coordinate systems for calculations.

* Geocentric Ecliptical Spherical (latitude, longitude)
* Geocentric Equatorial Spherical (rightAscension, declination)
* Geocentric Equatorial Rectangular (X, Y, Z)
* Heliocentric Ecliptical Spherical (latitude, longitude)
* Heliocentric Ecliptical Rectangular (X, Y, Z)
* Heliocentric Equatorial Rectangular (x, y, z)
* Local Horizontal (azimuth, altitude)

Each class provides methods to transform one coordinate system into another.

**Example 1**: Convert Geocentric Equatorial Spherical Coordinates into Geocentric Ecliptical Spherical Coordinates

```php
$T = -0.12727429842574; // Julian Centuries since J2000 (1987-04-10 19:21:00)
$rightAscension = 116.328942;
$declination = 28.026183;

$geoEquSphCoord = new GeocentricEquatorialSphericalCoordinates($rightAscension, $declination);
$geoEclSphCoord = $geoEquSphCoord->getGeocentricEclipticalSphericalCoordinates($T);

$lat = $geoEclSphCoord->getLatitude();
$lon = $geoEclSphCoord->getLongitude();
```

**Example 2**: Convert Geocentric Equatorial Spherical Coordinates to Local Horizontal Coordinates

```php
$location = new Location(38.921389, -77.065556); // Washington DC
$T = -0.12727429842574; // Julian Centuries since J2000 (1987-04-10 19:21:00)
$rightAscension = 347.3193375;
$declination = -6.719891667;

$geoEquSphCoord = new GeocentricEquatorialSphericalCoordinates($rightAscension, $declination);
$locHorCoord = $geoEquSphCoord->getLocalHorizontalCoordinates($location, $T);

$altitude = $locHorCoord->getAltitude();
$azimuth = $locHorCoord->getAzimuth();
```

## Astronomical Objects

An astronomical object **must** be initialized with the TOI. If you don't pass the TOI in the constructor, the
**current** time is chosen.

```php
$dateTime = new \DateTime('2017-07-02 12:00:00');
$toi = new TimeOfInterest($dateTime);

$moon = new Moon($toi);
```

### Sun

**Example 1**: Calculate the position of the sun for 17 May 2019 at 17:50 UTC

```php
$dateTime = new DateTime('2019-05-17 17:50');
$toi = new TimeOfInterest($dateTime);

$sun = new Sun($toi);

$geoEclSphCoordinates = $sun->getGeocentricEclipticalSphericalCoordinates();
$lat = $geoEclSphCoordinates->getLatitude();
$lon = $geoEclSphCoordinates->getLongitude();
```

The result of the calculation should be:
*Latitude: 0.0001°*
*Altitude: 56.544°*

**Example 2**: Calculate azimuth and altitude of the sun observed in Berlin, Germany for 17 May 2019 at 17:50 UTC

```php
$dateTime = new DateTime('2019-05-17 17:50');
$toi = new TimeOfInterest($dateTime);

$location = new Location(52.524, 13.411); // Berlin

$sun = new Sun($toi);

$locHorCoord = $sun->getLocalHorizontalCoordinates($location);
$azimuth = $locHorCoord->getAzimuth();
$altitude = $locHorCoord->getAltitude();
```

The result of the calculation should be:
*Azimuth: 291.0°*
*Altitude: 8.4°*

## Events

### Solar eclipse

TODO: Write some nice documentation :)