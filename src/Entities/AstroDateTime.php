<?php


namespace Andrmoel\AstronomyBundle\Entities;


class AstroDateTime
{
    public $year = 0;
    public $month = 0;
    public $day = 0;
    public $hour = 0;
    public $minute = 0;
    public $second = 0;

    public function __construct(
        int $year = null,
        int $month = 0,
        int $day = 0,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ) {
        if ($year === null) {
            $dateTime = new \DateTime();

            $this->year = (int)$dateTime->format('Y');
            $this->month = (int)$dateTime->format('m');
            $this->day = (int)$dateTime->format('d');
            $this->hour = (int)$dateTime->format('H');
            $this->minute = (int)$dateTime->format('i');
            $this->second = (float)$dateTime->format('s');
        } else {
            $this->year = $year;
            $this->month = $month;
            $this->day = $day;
            $this->hour = $hour;
            $this->minute = $minute;
            $this->second = $second;
        }
    }

    public static function createFromDateTime(\DateTime $dateTime): self
    {
        $year = (int)$dateTime->format('Y');
        $month = (int)$dateTime->format('m');
        $day = (int)$dateTime->format('d');
        $hour = (int)$dateTime->format('H');
        $minute = (int)$dateTime->format('i');
        $second = (float)$dateTime->format('s');

        return new AstroDateTime($year, $month, $day, $hour, $minute, $second);
    }

    public function __toString(): string
    {
        $year = $this->year;
        $month = str_pad($this->month, 2, '0', STR_PAD_LEFT);
        $day = str_pad($this->day, 2, '0', STR_PAD_LEFT);
        $hour = str_pad($this->hour, 2, '0', STR_PAD_LEFT);
        $minute = str_pad($this->minute, 2, '0', STR_PAD_LEFT);
        $second = str_pad($this->second, 2, '0', STR_PAD_LEFT);

        $string = "$year-$month-$day $hour:$minute:$second";

        return $string;
    }
}
