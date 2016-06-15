<?php
/**
 * Created by PhpStorm.
 * User: amado
 * Date: 5/21/16
 * Time: 10:13 PM
 */

function removeAccents($str)
{
    return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

function json_tosql($jsonarr, $colnames, PDOStatement $stmt)
{
    foreach($jsonarr as $jsonobj)
    {
        $vals   =   [];
        foreach($colnames as $col)
        {
            $vals[]     =   trim(removeAccents($jsonobj->$col));
        }
        $stmt->execute($vals);
    }
}

function printcsv($mode, $filename, PDO $pdo)
{

    static $stmts   =   [];
    $columns    =   [
        'hotels'         =>  ['code', 'name', 'country', 'zipcode', 'address', 'destination', 'latitude', 'longitude', 'currencycode', 'stars', 'hotel_type'],
        'destinations'   =>  ['code', 'country', 'parent', 'name', 'latitude', 'longitude']
    ];

    if(!isset($stmts[$mode]))
    {

        $qstr   =   sprintf('INSERT INTO %s (%s) VALUES ( ? %s);',
            $mode,
            implode(', ', $columns[$mode]),
            str_repeat(', ?', count($columns[$mode])-1)
        );

        $stmts[$mode]   =   $pdo->prepare($qstr);
    }
    $stmt   =   $stmts[$mode];

    switch($mode)
    {
        case 'destinations':
        case 'hotels':
                json_tosql(
                    json_decode(file_get_contents($filename)),
                    $columns[$mode],
                    $stmt);
            break;
    }
}

if($argc < 4)
{
    echo "\nThis prints a csv dump based on a given a list of json filenames:\n\n\tExample:\n\t\t";
    die("$argv[0] [hotel|destination] hotelpro.sqlite file1.json file2.json\n\n");
}

$mode   =   $argv[1];
$fname  =   $argv[2];



if(!file_exists($fname))
{
    $pdo    =   new PDO('sqlite:' . $fname);
    $tbls   =   [ <<<HDOC
create table hotels (
    code TEXT PRIMARY KEY ASC,
    name TEXT,
    country TEXT,
    zipcode TEXT,
    address TEXT,
    destination TEXT,
    latitude TEXT,
    longitude TEXT,
    currencycode TEXT,
    stars INTEGER,
    hotel_type INTEGER);
HDOC
        , <<<HDOC
create table destinations (
    code TEXT PRIMARY KEY ASC,
    country TEXT,
    parent TEXT,
    name TEXT,
    latitude TEXT, longitude TEXT
);
HDOC
    ];

    foreach($tbls as $tsql)
    {
        $pdo->query($tsql);
    }
}else{

    $pdo    =   new PDO('sqlite:' . $fname);
}

for($i=3; $i<$argc; $i++)
{
    printcsv($mode, $argv[$i], $pdo);
}