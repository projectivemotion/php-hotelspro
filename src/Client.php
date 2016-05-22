<?php
/**
 * Created by PhpStorm.
 * User: amado
 * Date: 5/21/16
 * Time: 8:22 PM
 */

namespace projectivemotion\HotelsPro;


class Client
{
    protected $username = '';
    protected $password = '';

    /**
     * @var \PDO
     */
    protected $DB   =   NULL;
    protected $api_path =   '/api/v2';
    protected $api_hostname =   'api-test.hotelspro.com';

    public function getDB()
    {
        if(!$this->DB)
        {
            throw new \Exception();
        }
        return $this->DB;
    }

    public function setDB(\PDO $DB)
    {
        $this->DB = $DB;
    }

    public function findHotelByName($name)
    {
        $query = $this->getDB()->prepare('SELECT * FROM hotels WHERE name = ?');
        $query->execute([$name]);

        $hotelObj   =   $query->fetchObject('\\projectivemotion\\HotelsPro\\Hotel');
        if(!$hotelObj)
            throw new Exception('Hotel "' . $name . '" not found in HotelsPro Datase');

        return $hotelObj;
    }

    protected function getResponse($method, $params = [])
    {
        $url    =
            sprintf("https://%s:%s@%s%s/%s",
                urlencode($this->username),
                ($this->password),
                $this->api_hostname,
                $this->api_path . $method,
                $params ? '?' . http_build_query($params) : ''
            );

        $response   =   file_get_contents($url
            );

        return $response;
    }

    public function Search(Query $query)
    {
        $result =   $this->getResponse('/search', $query->getParams());

        return json_decode($result);
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function __construct($username = '', $password = '')
    {
        $this->setPassword($password);
        $this->setUsername($username);
    }
}