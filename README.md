# Introduction

The astronomy-bundle provides methods for.

Install the package with:
```console
composer require andrmoel/astronomy-bundle
```

## Usage

### Angle Utils

```php
AngleUtils
```

### Time of Interest

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
* Get Greenwich (mean) Siderial Time

### Location

The location object represents the location of the observer on the earth's surface.

### Coordinate Systems

The bundle provides .... The classes are providing methods to transform one coordinate system into another one.

* Ecliptical Coordinates (latitude, longitude)
* 

### Astronomical Objects

An astronomical object **must** be initialized with the TOI. If you don't pass the TOI in the constructor, the
**current** time is chosen.

```php
$dateTime = new \DateTime('2017-07-02 12:00:00');
$toi = new TimeOfInterest($dateTime);

$moon = new Moon($toi);
```

#### Earth