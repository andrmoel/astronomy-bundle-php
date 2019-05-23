## Table of Contents  
1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Example data](#example)
4. [Angle Util](#angle)
5. [Time of Interest](#toi)
    1. [Julian Day, Centuries & Millennia](#toiJulianDay)
    1. [GMST, GAST & Equation of Time](#toiGmst)
6. [Location](#location)
7. [Coordinate Systems (and transformations)](#coordinates)
8. [Astronomical Objects](#objects)
    1. [Sun](#sun)
        1. [Position](#sunPosition)
        2. [Distance to earth](#sunDistance)
        3. [Sunrise, Sunset & Culmination](#sunrise)
    2. [Moon](#moon)
        1. [Position](#moonPosition)
        2. [Distance to earth](#moonDistance)
        3. [Sunrise, Sunset & Culmination](#moonrise)
        4. [Phases](#moonPhases)
    3. [Planets](#planets)
        1. [Heliocentric position of a planet](#planetHelio)
        1. [Geocentric position of a planet](#planetGeo)
        1. [Rise, Set & Culmination](#planetRise)
9. [Events](#events)
    1. [Solar Eclipse](#solarEclipse)
        1. [Create a Solar Eclipse](#solarEclipseCreate)
        1. [Type, Obscuration, Magnitude, Duration](#solarEclipseType)
        1. [Contacts (C1, C2, MAX, C3, C4)](#solarEclipseContacts)
    2. [Lunar Eclipse](#lunarEclipse)
10. [Other calculations](#other)
    1. [Distance between two locations](#distance)
    1. [Nutatation of earth](#nutation)

<a name="introduction"></a>
# Introduction

* ![#f03c15](https://placehold.it/15/f03c15/000000?text=+) **ATTENTION**:
This bundle on Version 0.X.X is still in development. Use on your own risk.
A stable release will be provided with version 1.X.X.


<a name="installation"></a>
# Installation

Use composer to install this package.

```console
composer require andrmoel/astronomy-bundle
```

<a name="example"></a>
# Example data

Some example calculations are provided inside the `/examples` folder of the project dir. Usage:

```
php examples/sun.php
```

<a name="angle"></a>
# Angle Util

The angle util provides helper methods to convert an angle into different formats.

**Example 1**: Convert decimal angle

```php
$angleDec = 132.6029282;

$angle = AngleUtil::dec2angle($angleDec);
$time = AngleUtil::dec2time($angleDec);
```

The result of the calculation should be:\
*Angle: 132°36'10.542"*\
*Time: 8h50m24.703s*

**Example 2**: Convert time into decimal format

```php
$time = '8h50m24.703s';

$angle = AngleUtil::time2dec($time);
```

The result of the calculation should be:\
*Angle: 132.60292916667°*

<a name="toi"></a>
# Time of Interest

The time of interest (TOI) object represents the time for which all of the astronomical calculations are done.
E.g. If you want to calculate the position of the sun for 02 July 2017 at 12:00:00 UTC, you need to initialize
the TOI as follow:

```
$dateTime = new DateTime('2017-07-02 12:00:00');
$toi = new TimeOfInterest($dateTime);
```

The following example show how to create a TOI-object which correspondends to the **current** date and time:

```php
$toi = new TimeOfInterest();
```

<a name="toiJulianDay"></a>
### Julian Day, Julian Centuries from J2000 and Julian Millennia from J2000

**Example**: Create TOI for 02 July 2017 at 13:37 UTC

```php
$dateTime = new \DateTime('2017-07-02 13:37:00'); // DateTime in UTC
$toi = new TimeOfInterest($dateTime);

$JD = $toi->getJulianDay();
$JD0 = $toi->getJulianDay0();
$T = $toi->getJulianCenturiesFromJ2000();
$t = $toi->getJulianMillenniaFromJ2000();
```

The result of the calculation should be:\
*Julian Day: 2457937.0673611*\
*Julian Day 0: 2457936.5*\
*Julian Centuries J2000: 0.1750052665602*\
*Julian Millennia J2000: 0.01750052665602*

<a name="toiGmst"></a>
## Greenwich Mean Sidereal Time (GMST), Greenwich Apparent Sidereal Time (GAST) and Equation of Time

With the help of the TOI-Object it is possible to calculate the GMST, GAST and the equation in time (*units of all values are degrees*).
The following example explains how to get these values for 20 December 1992 at 00:00 UTC.

```php
$dateTime = new \DateTime('1992-12-20 00:00:00');
$toi = new TimeOfInterest($dateTime);

$GMST = $toi->getGreenwichMeanSiderealTime();
$GAST = $toi->getGreenwichApparentSiderealTime();
$E = $toi->getEquationOfTime();
```

The result of the calculation should be:\
*GMST: 88.82536°*\
*GAST: 88.829629°*\
*Equation of Time: 0.619485°*

You may convert this values into the more common angle format by using `AngleUtil::dec2time($value)`.
The result will be:\
*GMST: 5h55m18.086s*\
*GAST: 5h55m19.111s*\
*Equation of Time: 0h2m28.676s*

<a name="location"></a>
# Location

The location object represents the location of the observer on the earth's surface.

```php
// Initialize Location object for Berlin
$location = new Location(52.524, 13.411);

// Initialize Location with elevation (Mt. Everest)
$location = new Location(27.98787, 86.92483, 8848);
```

<a name="coordinates"></a>
# Coordinate systems and transformations

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

<a name="objects"></a>
# Astronomical Objects

An astronomical object **must** be initialized with the TOI. If you don't pass the TOI in the constructor, the
**current** time is chosen.

```php
$dateTime = new \DateTime('2017-07-02 12:00:00');
$toi = new TimeOfInterest($dateTime);

$moon = new Moon($toi);
```

<a name="sun"></a>
## Sun

<a name="sunPosition"></a>
### Position of the sun

**Example 1**: Calculate the position of the sun for 17 May 2019 at 17:50 UTC

```php
$dateTime = new DateTime('2019-05-17 17:50');
$toi = new TimeOfInterest($dateTime);

$sun = new Sun($toi);

$geoEclSphCoordinates = $sun->getGeocentricEclipticalSphericalCoordinates();
$lat = $geoEclSphCoordinates->getLatitude();
$lon = $geoEclSphCoordinates->getLongitude();
```

The result of the calculation should be:\
*Latitude: 0.0001°*\
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

The result of the calculation should be:\
*Azimuth: 291.0°*\
*Altitude: 8.4°*

<a name="sunDistance"></a>
### Distance of the sun to earth

**Example 1**: The current distance of the sun in kimometers can be calculated as follow:

```php
$sun = new Sun();

$distance = $sun->getDistanceToEarth();
```

The result should be between 147.1 mio and 152.1 mio kilometers.

**Example 2**: Get the distance of the sun on 05 June 2017 at 20:50 UTC

```php
$dateTime = new DateTime('2017-06-05 20:50');
$toi = new TimeOfInterest($dateTime);

$sun = new Sun($toi);

$distance = $sun->getDistanceToEarth();
```

The result should be 151797703km.

<a name="sunrise"></a>
### Sunrise, sunset and upper culmination

**Example**: Calculate sunrise, sunset and upper culmination for Berlin, Germany for 17 May 2019

```php
$dateTime = new DateTime('2019-05-17');
$toi = new TimeOfInterest($dateTime);

$location = new Location(52.524, 13.411); // Berlin

$sun = new Sun($toi);

// Results are TimeOfInterest objects
$sunrise = $sun->getSunrise($location);
$sunset = $sun->getSunset($location);
$upperCulmination = $sun->getUpperCulmination($location);
```

The result of the calculation should be:\
*Sunrise: 03:08 UTC*\
*Sunset: 18:59 UTC*\
*Upper culmination: 13:03 UTC*

<a name="moon"></a>
## Moon

<a name="moonPosition"></a>
### Position of the moon

The position of the moon can be calculated as explained in the following example.

```php
$dateTime = new DateTime('1992-04-12 00:00:00');
$toi = new TimeOfInterest($dateTime);

$moon = new Moon($toi);

$geoEquSphCoord = $moon->getGeocentricEquatorialSphericalCoordinates();
$rightAscension = $geoEquSphCoord->getRightAscension();
$declination = $geoEquSphCoord->getDeclination();
```

The result of the calculation should be:\
*Right ascension: 134.69°*\
*Declination: 13.77°*

<a name="moonDistance"></a>
### Distance of the moon to earth

**Example 1**: The current distance of the moon in kimometers can be calculated as follow:

```php
$moon = new Moon();

$distance = $moon->getDistanceToEarth();
```

The result should be between 363300km and 405500km.

**Example 2**: Get the distance of the moon on 05 June 2017 at 20:50 UTC

```php
$dateTime = new DateTime('2017-06-05 20:50');
$toi = new TimeOfInterest($dateTime);

$moon = new Moon($toi);

$distance = $moon->getDistanceToEarth();
```

The result should be 402970km.

<a name="moonrise"></a>
### Moonrise, moonset and upper culmination

* ![#f03c15](https://placehold.it/15/f03c15/000000?text=+) **ATTENTION**: Feature not yet implemented

<a name="moonPhases"></a>
### Phases of the moon

The following code sniped explains how to calculate all important parameters which belong to the moons phase
for an specific date. In this example it is 13 May 2019 at 21:30 UTC.

```php
$dateTime = new DateTime('2019-05-13 21:30:00');
$toi = new TimeOfInterest($dateTime);

$moon = new Moon($toi);

$isWaxing = $moon->isWaxingMoon();
$illumination = $moon->getIlluminatedFraction();
$positionAngle = $moon->getPositionAngleOfMoonsBrightLimb();
```

The result of the calculation should be:\
*Is waxing moon: yes*\
*Illumination: 0.709 (70.9%)*\
*Position angle of bright limb: 293.54°*

<a name="planets"></a>
## Planets

Like Sun and Moon-Objects, the Planets can be created by passing the TimeOfInterest.
If no TimeOfInteressed is passed, the **current date and time** are used for further calculations.

**Example**: Create some planets

```php
$dateTime = new DateTime('2018-06-03 19:00:00'); // UTC
$toi = new TimeOfInterest($dateTime);

$mercury = new Mercury();  // Time = now
$venus = new Venus($toi); // Time = 2018-06-03 19:00:00
$earth = new Earth($toi);
$mars = new Mars($toi);
$jupiter = new Jupiter($toi);
$saturn = new Saturn($toi);
$uranus = new Uranus($toi);
$neptune = new Neptune($toi);
```

<a name="planetHelio"></a>
### Heliocentric position of a planet

The calculations use the VSOP87 theory to obtain the heliocentric position of a planet.

**Example**: Calculate the heliocentric position of Venus for 20. December 1992 at 00:00 UTC.

```php
$dateTime = new DateTime('1992-12-20 00:00:00');
$toi = new TimeOfInterest($dateTime);

$venus = new Venus($toi);

$helEclSphCoord = $venus->getHeliocentricEclipticalSphericalCoordinates();
$lat = $helEclSphCoord->getLatitude();
$lon = $helEclSphCoord->getLongitude();
$r = $helEclSphCoord->getRadiusVector();

$helEclRecCoord = $venus->getHeliocentricEclipticalRectangularCoordinates();
$x = $helEclRecCoord->getX();
$y = $helEclRecCoord->getY();
$z = $helEclRecCoord->getZ();
```
The result of the calculation should be:\
*Latitude: -2.62063°*\
*Longitude: 26.11412°*\
*Radius vector: 0.72460*\
*X: 0.64995327095595*\
*Y: 0.31860745636351*\
*Z: -0.033130385747949

<a name="planetGeo"></a>
### Geocentric position of a planet

* ![#f03c15](https://placehold.it/15/f03c15/000000?text=+) **ATTENTION**: Feature not yet implemented

TODO: Write some nice documentation :)

<a name="planetRise"></a>
### Rise, set and upper culmination

* ![#f03c15](https://placehold.it/15/f03c15/000000?text=+) **ATTENTION**: Feature not yet implemented

TODO: Write some nice documentation :)

<a name="events"></a>
# Events

<a name="solarEclipse"></a>
## Solar eclipse

<a name="solarEclipseCreate"></a>
### Create a Solar Eclipse object

**Example**: Create a solar eclipse for 21 August 2017 for the location Madrads in Oregon (USA)

```php
$location = new Location(44.61040, -121.23848); // Madras, OR

$dateTime = new DateTime('2017-08-21'); // Date of the eclipse (UTC)
$toi = new TimeOfInterest($dateTime);

$solarEclipse = SolarEclipse::create($toi, $location);
```

*Note: If the date of the eclipse is invalid, an exception will be thrown.*

<a name="solarEclipseType"></a>
### Eclipse type, Obscuration, Magnitude, Duration, etc.

To obtain the eclipse circumstances of the **maximum eclipse** for a given location, see the following examples.

The **type of an eclipse** (for the given location) is expressed in a string. But it is better to use the following constants:
`SolarEclipse:TYPE_NONE`,
`SolarEclipse:TYPE_PARTIAL`,
`SolarEclipse:TYPE_ANNULAR` or
`SolarEclipse:TYPE_TOTAL`.

**Example 1**: Local circumstances for the total solar eclipse of 21 August 2017 for Madras, OR

```php
$location = new Location(44.61040, -121.23848); // Madras, OR

$dateTime = new DateTime('2017-08-21'); // Date of the eclipse (UTC)
$toi = new TimeOfInterest($dateTime);

$solarEclipse = SolarEclipse::create($toi, $location);

$type = $solarEclipse->getEclipseType();
$duration = $solarEclipse->getEclipseDuration(); // in seconds
$durationTotality = $solarEclipse->getEclipseUmbraDuration(); // in seconds
$obscuration = $solarEclipse->getObscuration();
$magnitude = $solarEclipse->getMagnitude();
$moonSunRatio = $solarEclipse->getMoonSunRatio();
```

The result of the calculation should be:\
*Type: total*\
*Duration of eclipse: 9257s*\
*Duration of totality: 120s*\
*Obscuration: 1 (100%)*\
*Magnitude: 1.01*\
*Moon-sun-ratio: 1.03*

**Example 2**: Local circumstances for the partial solar eclipse of 20 March 2015 in Berlin

```php
$location = new Location(52.52, 13.405); // Berlin

$dateTime = new DateTime('2015-03-20'); // Date of the eclipse (UTC)
$toi = new TimeOfInterest($dateTime);

$solarEclipse = SolarEclipse::create($toi, $location);

$type = $solarEclipse->getEclipseType();
$duration = $solarEclipse->getEclipseDuration(); // in seconds
$durationTotality = $solarEclipse->getEclipseUmbraDuration(); // in seconds
$obscuration = $solarEclipse->getObscuration();
$magnitude = $solarEclipse->getMagnitude();
$moonSunRatio = $solarEclipse->getMoonSunRatio();
```

The result of the calculation should be:\
*Type: partial*\
*Duration of eclipse: 8386s*\
*Duration of totality: 0s*\
*Obscuration: 0.74 (74%)*\
*Magnitude: 0.79*\
*Moon-sun-ratio: 1.05*

<a name="solarEclipseContact"></a>
### Contacts (C1, C2, MAX, C3, C4)

TODO: Write some nice documentation :)

<a name="lunarEclipse"></a>
# Lunar eclipse

* ![#f03c15](https://placehold.it/15/f03c15/000000?text=+) **ATTENTION**: Feature not yet implemented

TODO: Write some nice documentation :)

<a name="other"></a>
# Other calculations

<a name="distance"></a>
## Distance between two locations

```php
$location1 = new Location(52.524, 13.411); // Berlin
$location2 = new Location(40.697,-74.539); // New York

$distance = EarthCalc::getDistanceBetweenLocations($location1, $location2);
```

The result of the calculation should be 6436km.

<a name="nutation"></a>
## Nutation of earth

```php
$T = -0.127296372458;

$nutationLon = EarthCalc::getNutationInLongitude($T);
$nutationLon = AngleUtil::dec2angle($nutationLon);

$nutationObl = EarthCalc::getNutationInObliquity($T);
$nutationObl = AngleUtil::dec2angle($nutationObl);
```

The result of the calculation should be:\
*Nutation in longitude: -0°0'3.788"*\
*Nutation in obliquity: 0°0'9.442"*
