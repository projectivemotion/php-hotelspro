<?php
/**
 * Project: PHPHotelsPro
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

namespace projectivemotion\HotelsPro;

class Hotel
{
    public $code;
    public $name;
    public $country;
    public $zipcode;
    public $address;
    public $destination;
    public $latitude;
    public $longitude;
    public $currencycode;
    public $stars;
    public $hotel_type;

    // provided by findHotelsBy
    public $city;
}