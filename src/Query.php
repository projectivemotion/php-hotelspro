<?php
/**
 * Created by PhpStorm.
 * User: amado
 * Date: 5/21/16
 * Time: 8:28 PM
 */

namespace projectivemotion\HotelsPro;


class Query
{
    protected $format;
    protected $pax;
    protected $checkout;
    protected $checkin;
    protected $currency;
    protected $client_nationality;
    protected $limit;
    protected $hotel_code;

    public function setHotelCode($hotel_code)
    {
        $this->hotel_code = $hotel_code;
    }

    public function setCheckin($checkin)
    {
        $this->checkin = $checkin;
    }

    public function setCheckout($checkout)
    {
        $this->checkout = $checkout;
    }

    public function setClientNationality($client_nationality)
    {
        $this->client_nationality = $client_nationality;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setPax($pax)
    {
        $this->pax = $pax;
    }

    public function getCheckin()
    {
        return $this->checkin;
    }

    public function getCheckout()
    {
        return $this->checkout;
    }

    public function getClientNationality()
    {
        return $this->client_nationality;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getPax()
    {
        return $this->pax;
    }

    public function getParams()
    {
        return get_object_vars($this);
    }
}
