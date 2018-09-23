<?php

$file = __DIR__ . '/../src/Resources/vsop87/plain/VSOP87D.jup';

$fileHandle = fopen($file, 'r');

$data = array();

while (!feof($fileHandle)) {
    $row = fgets($fileHandle);

    $type = substr($row, 3, 2);
    if (preg_match('/^([0-9]{1})([0-9]{1})$/', $type, $matches)) {
        switch ($matches[1][0]) {
            case '1':
                $parameter = 'L';
                break;
            case '2':
                $parameter = 'B';
                break;
            case '3':
                $parameter = 'R';
                break;
            default:
                die("ERRRRRRRRRRRROR");
        }

        $number = (int)$matches[2][0];

        if (preg_match('/([0-9.]+) +([0-9.]+) +([0-9.]+) $/', $row, $matches2)) {
            $A = (float)$matches2[1];
            $B = (float)$matches2[2];
            $C = (float)$matches2[3];

            $term = [$A, $B, $C];

            $data[$parameter][$number][] = $term;
        }
    }
}

fclose($fileHandle);

file_put_contents(__DIR__ . '/../src/Resources/vsop87/json/jupiter.json', json_encode($data));

var_dump($data['R']);