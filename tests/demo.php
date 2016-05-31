<?php
/**
 * Project: PHPHotelsPro
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

// copied this from doctrine's bin/doctrine.php
$autoload_files = array( __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php');

foreach($autoload_files as $autoload_file)
{
    if(!file_exists($autoload_file)) continue;
    require_once $autoload_file;
}
// end autoloader finder

if($argc < 3)
    die("$argv[0] user pass [test]");

if(!file_exists('hotelspro.sqlite'))
{
    die("hotelspro.sqlite must be created before calling this demo. see util/create-sqlite.sh");
}

$username   =   $argv[1];
$password   =   $argv[2];

$searchq    =   new \projectivemotion\HotelsPro\Query();

$client = new \projectivemotion\HotelsPro\Client($username, $password);

if($argc > 3 && $argv[3] ==  'TEST')
    $client->setTestApi();

$client->setDB(new \PDO('sqlite:hotelspro.sqlite'));
$searchq->setCheckin(date('Y-m-d', time()+60*60*24*7));
$searchq->setCheckout(date('Y-m-d', time()+60*60*24*7*3));
$searchq->setClientNationality('us');
$searchq->setCurrency('EUR');
$searchq->setPax('2');

$searchq->setHotelCode(
    $client->findHotelByName('Nova Hotel')->code
);

$result =   $client->Search($searchq);

print_r($result);
