<?php

namespace Andrmoel\AstronomyBundle\Test;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEquatorialCorrections;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class LunarZhb
{
    private $fontFile = __DIR__ . '/../../examples/OpenSans-Regular.ttf';

    private $location;
    private $month;
    private $year;
    private $timeStepsInMin = 15;

    private $xRowWidth = 20;
    private $yRowHeight = 5;

    // Header
    private $headerHeight = 50;

    // Plot
    private $xLegendHeight = 25;
    private $yLegendWidth = 50;

    // Temp
    // TODO
    private $daysPerMonth = 30;
    private $width;
    private $height;

    public function setMonth($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function setLocation(Location $location)
    {
        $this->location = $location;
    }

    public function createImage()
    {
        $image = $this->getBaseImage();

        $this->drawHeader($image);
        $this->drawLegendX($image);
        $this->drawLegendY($image);
        $this->drawPlot($image);
        $this->drawGrid($image);

        unlink('image.png');
        imagepng($image, 'image.png');
    }

    public function getBaseImage()
    {
        $this->width = $this->daysPerMonth * $this->xRowWidth + $this->yLegendWidth;
        $this->height = 24 * 60 / $this->timeStepsInMin * $this->yRowHeight + $this->headerHeight + $this->xLegendHeight;

        $image = imagecreatetruecolor($this->width, $this->height);

        $color = imagecolorallocate($image, 50, 50, 50);
        imagefilledrectangle($image, 0, 0, $this->width, $this->height, $color);

        return $image;
    }

    private function drawHeader(&$image)
    {
        $color = imagecolorallocate($image, 255, 255, 255);

        $text = 'Möglicher ZHB am Mond - Bad Schönborn';
        imagettftext($image, 12, 0, 10, 25, $color, $this->fontFile, $text);

        $text = 'Monat: ' . $this->month . '.' . $this->year;
        imagettftext($image, 11, 0, 10, 48, $color, $this->fontFile, $text);
    }

    private function drawLegendX(&$image)
    {
        for ($day = 0; $day < $this->daysPerMonth; $day++) {
            $text = str_pad($day + 1, 2, '0', STR_PAD_LEFT);

            $box = imagettfbbox(10, 0, $this->fontFile, $text); // TODO ...

            $x = $day * $this->xRowWidth + $this->yLegendWidth + 3;
            $y = $this->headerHeight + 20;

            $color = imagecolorallocate($image, 230, 230, 230);
            imagettftext($image, 10, 0, $x, $y, $color, $this->fontFile, $text);
        }
    }

    private function drawLegendY(&$image)
    {
        for ($min = 0; $min < 60 * 24; $min += $this->timeStepsInMin) {
            if ($min % 60 !== 0) {
                continue;
            }

            $x = 5;
            $y = $min / $this->timeStepsInMin * $this->yRowHeight + $this->headerHeight + $this->xLegendHeight + 15;

            $hourStr = str_pad(floor($min / 60), 2, '0', STR_PAD_LEFT);
            $minStr = str_pad($min % 60, 2, '0', STR_PAD_LEFT);

            $text = $hourStr . ':' . $minStr;

            $color = imagecolorallocate($image, 230, 230, 230);
            imagettftext($image, 10, 0, $x, $y, $color, $this->fontFile, $text);
        }
    }

    private function drawGrid(&$image)
    {
        // Grid for x-axis
        for ($day = 0; $day < $this->daysPerMonth; $day++) {
            $x1 = $day * $this->xRowWidth + $this->yLegendWidth;
            $y1 = $this->headerHeight;
            $x2 = $x1;
            $y2 = $this->height;

            $color = imagecolorallocate($image, 70, 70, 70);
            imageline($image, $x1, $y1, $x2, $y2, $color);
        }

        // Grid for y-axis
        for ($min = 0; $min < 60 * 24; $min += $this->timeStepsInMin) {
            if ($min % 60 !== 0) {
                continue;
            }

            $x1 = 0;
            $y1 = $min / $this->timeStepsInMin * $this->yRowHeight + $this->headerHeight + $this->xLegendHeight;
            $x2 = $this->width;
            $y2 = $y1;

            $color = imagecolorallocate($image, 70, 70, 70);
            imageline($image, $x1, $y1, $x2, $y2, $color);
        }
    }

    public function drawPlot(&$image)
    {
        for ($day = 0; $day < $this->daysPerMonth; $day++) {
            $x1 = $day * $this->xRowWidth + $this->yLegendWidth;
            $x2 = $day * $this->xRowWidth + $this->xRowWidth + $this->yLegendWidth;

            $toi = new TimeOfInterest();
            $toi->setTime($this->year, $this->month, $day + 1);

            $moon = new Moon($toi);

            if ($moon->getIlluminatedFraction() >= 0.4) {
                for ($min = 0; $min < 60 * 24; $min += $this->timeStepsInMin) {
                    $y1 = $min / $this->timeStepsInMin * $this->yRowHeight
                        + $this->headerHeight
                        + $this->xLegendHeight;
                    $y2 = $min / $this->timeStepsInMin * $this->yRowHeight
                        + $this->yRowHeight
                        + $this->headerHeight
                        + $this->xLegendHeight;;

                    $toi = new TimeOfInterest();
                    $toi->setTime($this->year, $this->month, $day + 1, 0, $min);

                    var_dump($toi->getDateTime()->format('Y-m-d H:i:s'));

                    if ($this->getTwilight($toi) > Sun::TWILIGHT_DAY) {
                        $altitude = $moon
                            ->getGeocentricEquatorialCoordinates()
                            ->getLocalHorizontalCoordinates($this->location, $toi)
                            ->getAltitude();

                        // CHA of moon is only visible > 58° altitude
                        if ($altitude >= 58) {
                            $color = imagecolorallocate($image, 50, 50, 50);

                            if ($altitude >= 58.5) {
                                $color = imagecolorallocate($image, 100, 100, 100);
                            }

                            if ($altitude >= 59) {
                                $color = imagecolorallocate($image, 150, 150, 150);
                            }

                            if ($altitude >= 59.5) {
                                $color = imagecolorallocate($image, 200, 200, 200);
                            }

                            if ($altitude >= 60) {
                                $color = imagecolorallocate($image, 250, 250, 250);
                            }

                            imagefilledrectangle($image, $x1, $y1, $x2, $y2, $color);
                        }
                    }
                }
            }
        }

        return $image;
    }

    public function run()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(2018, 12, 5, 11, 0, 0);

        $location = new Location(52.51345, 13.42632);

        $sun = new Sun($toi);

        $geoEquCoordinates = $sun->getGeocentricEquatorialCoordinates();

        $correction = new GeocentricEquatorialCorrections($toi);
        $geoEquCoordinates = $correction->correctCoordinates($geoEquCoordinates);

        $alt = $geoEquCoordinates->getLocalHorizontalCoordinates($location, $toi);

        var_dump($alt);
//
//        $geoEqCoordinates = $moon->getGeocentricEquatorialCoordinates();
////            ->getLocalHorizontalCoordinates($this->location, $toi)
////            ->getAltitude();
//
//        $correction = new GeocentricEquatorialCorrections($toi);
//        $geoEqCoordinatesCorrected = $correction->correctCoordinates($geoEqCoordinates);
//
//        var_dump($geoEqCoordinates, $geoEqCoordinatesCorrected);die();

//        $this->getMaximumMoonAltitude($toi);
        die();
        $this->createImage();
    }

    private function getMaximumMoonAltitude(TimeOfInterest $toi)
    {
        $cnt = 0; // TODO WEG
        $moon = new Moon($toi);

        $step = 120;

        $isHigher = true;
        $min = 0;

        $maxAltitude = -90;

        while($cnt <= 40) {
            $toi->setTime(2018, 12, 5, 0, $min, 0);
            $altitude = $moon
                ->getGeocentricEquatorialCoordinates()
                ->getLocalHorizontalCoordinates($this->location, $toi)
                ->getAltitude();

            if ($altitude > $maxAltitude) {
                echo "\n new max found: $altitude";
                $maxAltitude = $altitude;
            } else {
                $step -= 5;
            }

            echo("\n$min - $step - $maxAltitude");
            var_dump($min / 60);

            $min += $step;

            $cnt++;
        }
    }

    public function getTwilight(TimeOfInterest $toi): int
    {
        $sun = new Sun($toi);

        return $sun->getTwilight($this->location);
    }
}
