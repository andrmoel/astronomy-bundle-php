<?php

namespace Andrmoel\AstronomyBundle\Tests\Events\RiseSetTransit;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Jupiter;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Mars;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Mercury;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Neptune;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Saturn;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Uranus;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Events\RiseSetTransit\RiseSetTransit;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\Tests\Traits\InvokeTrait;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class RiseSetTransitTest extends TestCase
{
    use InvokeTrait;

    /**
     * @test
     */
    public function fix360CrossingTest()
    {
        date_default_timezone_set('Pacific/Auckland');
        $data = [
            ["day" => 19, "sunrise" => "2022-03-19T18:23:37.000+13:00", "sunset" => "2022-03-19T06:34:46.000+13:00"],
            ["day" => 20, "sunrise" => "2022-03-20T18:24:30.000+13:00", "sunset" => "2022-03-20T06:33:17.000+13:00"],
            ["day" => 21, "sunrise" => "2022-03-21T18:25:23.000+13:00", "sunset" => "2022-03-21T06:31:48.000+13:00"],
            ["day" => 22, "sunrise" => "2022-03-22T18:26:16.000+13:00", "sunset" => "2022-03-22T06:30:19.000+13:00"]
        ];

        foreach ($data as $datum) {
            $location = Location::create(-36.86297, 174.71185);
            $datetime = new DateTime("2022-03-" . $datum['day'] . "T00:00:00.000+00:00", new DateTimeZone("UTC"));

            $date = (clone($datetime))->setTime(...[23, 59, 59])->setTimezone(new DateTimeZone('UTC'));
            $toi = TimeOfInterest::createFromDateTime($date);

            $sun = Sun::create($toi);

            $rise = $sun->getSunrise($location);
            $set = $sun->getSunset($location);

            $this->assertEquals($datum['sunrise'],$rise->getDateTime()->format(DATE_RFC3339_EXTENDED));
            $this->assertEquals($datum['sunset'],$set->getDateTime()->format(DATE_RFC3339_EXTENDED));

        }
    }

    /**
     * @test
     * Meeus 15.a
     */
    public function getRiseTest()
    {
        $astronomicalObjects = [
            Sun::class   => ['2019-05-31', '2019-05-31 09:10:49'],
            Venus::class => ['1988-03-20', '1988-03-20 12:25:25'],
        ];

        $location = Location::create(42.3333, -71.0833);

        foreach ($astronomicalObjects as $astronomicalObject => $expectedToi) {
            $toi = TimeOfInterest::createFromString($expectedToi[0]);

            $riseSetTransit = new RiseSetTransit($astronomicalObject, $location, $toi);
            $toiRise = $riseSetTransit->getRise();

            $this->assertEquals($expectedToi[1], $toiRise);
        }
    }

    /**
     * @test
     * Meeus 15.a
     */
    public function getSetTest()
    {
        $astronomicalObjects = [
            Sun::class   => ['2019-05-31', '2019-05-31 00:12:49'],
            Venus::class => ['1988-03-20', '1988-03-20 02:54:39'],
        ];

        $location = Location::create(42.3333, -71.0833);

        foreach ($astronomicalObjects as $astronomicalObject => $expectedToi) {
            $toi = TimeOfInterest::createFromString($expectedToi[0]);

            $riseSetTransit = new RiseSetTransit($astronomicalObject, $location, $toi);
            $toiSet = $riseSetTransit->getSet();

            $this->assertEquals($expectedToi[1], $toiSet);
        }
    }

    /**
     * @test
     */
    public function getStandardAltitudeTest()
    {
        $astronomicalObjects = [
            Sun::class     => -0.8333,
            Mercury::class => -0.5667,
            Venus::class   => -0.5667,
            Mars::class    => -0.5667,
            Jupiter::class => -0.5667,
            Saturn::class  => -0.5667,
            Uranus::class  => -0.5667,
            Neptune::class => -0.5667,
        ];

        $location = Location::create(0, 0);
        $toi = TimeOfInterest::createFromCurrentTime();

        foreach ($astronomicalObjects as $astronomicalObject => $expectedStdAlt) {
            $riseSetTransit = new RiseSetTransit($astronomicalObject, $location, $toi);
            $stdAlt = $this->invokeMethod($riseSetTransit, 'getStandardAltitude');

            $this->assertEquals($expectedStdAlt, $stdAlt);
        }
    }

    /**
     * @test
     * Meeus 15.a
     */
    public function getTransitTest()
    {
        $astronomicalObjects = [
            Sun::class   => ['2019-05-31', '2019-05-31 16:42:01'],
            Venus::class => ['1988-03-20', '1988-03-20 19:40:30'],
        ];

        $location = Location::create(42.3333, -71.0833);

        foreach ($astronomicalObjects as $astronomicalObject => $expectedToi) {
            $toi = TimeOfInterest::createFromString($expectedToi[0]);

            $riseSetTransit = new RiseSetTransit($astronomicalObject, $location, $toi);
            $toiTransit = $riseSetTransit->getTransit();

            $this->assertEquals($expectedToi[1], $toiTransit);
        }
    }

}
