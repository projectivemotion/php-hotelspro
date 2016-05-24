#!/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: amado
 * Date: 5/21/16
 * Time: 10:13 PM
 */

function json_to_csv($jsonarr, $colnames, $fhandle)
{
    foreach($jsonarr as $jsonobj)
    {
        $vals   =   [];
        foreach($colnames as $col)
        {
            $vals[]     =   $jsonobj->$col;
        }
        fputcsv($fhandle, $vals,"|");
    }
}

function printcsv($mode, $filename)
{
    $columns    =   [
        'hotel'         =>  ['code', 'name', 'country', 'zipcode', 'address', 'destination', 'latitude', 'longitude', 'currencycode', 'stars', 'hotel_type'],
        'destination'   =>  ['code', 'country', 'parent', 'name', 'latitude', 'longitude']
    ];

    $fhandle    =   fopen('php://stdout', 'w');
    switch($mode)
    {
        case 'destination':
        case 'hotel':
                json_to_csv(
                    json_decode(file_get_contents($filename)),
                    $columns[$mode],
                    $fhandle);
            break;
    }

    fclose($fhandle);
}
if($argc < 3)
{
    echo "\nThis prints a csv dump based on a given a list of json filenames:\n\n\tExample:\n\t\t";
    die("$argv[0] [hotel|destination] file1.json file2.json\n\n");
}

$mode   =   $argv[1];

for($i=2; $i<$argc; $i++)
{
    printcsv($mode, $argv[$i]);
}