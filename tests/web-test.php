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

if(php_sapi_name() == 'cli')
{
    ?>
Call this script from web browser, you may use php's built in web server via:

$ php -S 127.0.0.1:8080 <?php echo $argv[0]; ?>


<?php
    exit;
}


// To use the WebUi in your project just copy/paste the following two lines and modify to fit your needs.

$pdo    =   new PDO('sqlite:hotelspro.sqlite');
\projectivemotion\HotelsPro\WebUi::Run('','',$pdo);