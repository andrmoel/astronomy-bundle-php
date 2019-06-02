<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class CoordinateTransformations
{
    public static function rectangular2spherical(float $x, float $y, float $z): array
    {
        // Meeus 33.2
        $longitudeRad = atan2($y, $x);
        $longitude = AngleUtil::normalizeAngle(rad2deg($longitudeRad));

        $latitudeRad = atan($z / sqrt(pow($x, 2) + pow($y, 2)));
        $latitude = rad2deg($latitudeRad);

        $radiusVector = sqrt(pow($x, 2) + pow($y, 2) + pow($z, 2));

        return [$longitude, $latitude, $radiusVector];
    }

    public static function spherical2rectangular(float $longitude, float $latitude, float $radiusVector): array
    {
        $longitudeRad = deg2rad($longitude);
        $latitudeRad = deg2rad($latitude);

        $x = $radiusVector * cos($latitudeRad) * cos($longitudeRad);
        $y = $radiusVector * cos($latitudeRad) * sin($longitudeRad);
        $z = $radiusVector * sin($latitudeRad);

        return [$x, $y, $z];
    }

    public static function eclipticalSpherical2equatorialSpherical(
        float $longitude,
        float $latitude,
        float $radiusVector,
        float $T
    ): array
    {
        $eps = EarthCalc::getTrueObliquityOfEcliptic($T);

        $epsRad = deg2rad($eps);
        $latitudeRad = deg2rad($latitude);
        $longitudeRad = deg2rad($longitude);

        // Meeus 13.3
        $n = sin($longitudeRad) * cos($epsRad) - (sin($latitudeRad) / cos($latitudeRad)) * sin($epsRad);
        $d = cos($longitudeRad);
        $rightAscensionRad = atan2($n, $d);
        $rightAscension = AngleUtil::normalizeAngle(rad2deg($rightAscensionRad));

        // Meeus 13.4
        $declinationRad = asin(
            sin($latitudeRad) * cos($epsRad) + cos($latitudeRad) * sin($epsRad) * sin($longitudeRad)
        );
        $declination = rad2deg($declinationRad);

        return [$rightAscension, $declination, $radiusVector];
    }

    public static function equatorialSpherical2eclipticalSpherical(
        float $rightAscension,
        float $declination,
        float $radiusVector,
        float $T
    ): array
    {
        $eps = EarthCalc::getTrueObliquityOfEcliptic($T);

        $epsRad = deg2rad($eps);
        $rightAscensionRad = deg2rad($rightAscension);
        $declinationRad = deg2rad($declination);

        // Meeus 13.1
        $n = sin($rightAscensionRad) * cos($epsRad) + tan($declinationRad) * sin($epsRad);
        $d = cos($rightAscensionRad);
        $longitudeRad = atan2($n, $d);
        $longitude = AngleUtil::normalizeAngle(rad2deg($longitudeRad));

        // Meeus 13.2
        $latitudeRad = asin(
            sin($declinationRad) * cos($epsRad) - cos($declinationRad) * sin($epsRad) * sin($rightAscensionRad)
        );
        $latitude = rad2deg($latitudeRad);

        return [$longitude, $latitude, $radiusVector];
    }
}
