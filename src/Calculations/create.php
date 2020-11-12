<?php

require '../../vendor/autoload.php';

$handle = opendir(__DIR__ . '/../Resources/besselianElements/');

$data = [];
while ($file = readdir($handle)) {
    if (preg_match('/([0-9-]+)([0-9]{2})([0-9]{2}).txt/', $file, $matches)) {
        $time = new \Andrmoel\AstronomyBundle\Entities\Time($matches[1], $matches[2], $matches[3]);

        $jd = \Andrmoel\AstronomyBundle\Calculations\TimeCalc::time2julianDay($time);

        $data[] = $jd;
    }
}

asort($data);

$string = '[' . implode($data, ',') . ']';

//var_dump($data);

file_put_contents(__DIR__ . '/foo.js', $string);
