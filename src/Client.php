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
    const HOTELCLASS    =   '\\projectivemotion\\HotelsPro\\Hotel';
    const MATCH_EXACT   =   0;
    const MATCH_LIKE    =   1;

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
        $query = $this->getDB()->prepare('
  SELECT h.*, d.name as city FROM hotels h
  INNER JOIN destinations d ON (d.code = h.destination)
  WHERE h.name = ? LIMIT 1');
        $query->execute([$name]);

        $hotelObj   =   $query->fetchObject(self::HOTELCLASS);
        if(!$hotelObj)
            throw new Exception('Hotel "' . $name . '" not found in HotelsPro Datase');

        return $hotelObj;
    }

    public function queryDB($query, $classname = '')
    {
        $qstmt  =   $this->getDB()->query($query);

        return $qstmt->fetchAll(\PDO::FETCH_CLASS, $classname ?: self::HOTELCLASS);
    }

    /**
     * @param $args
     * @return Hotel[]
     * @throws \Exception
     */
    public function findHotelsBy($args, $match_type = self::MATCH_EXACT)
    {
        $queryfmt  =   'SELECT
          h.*, coalesce(d1.name, d0.name) as city FROM hotels h
          INNER JOIN destinations d0 ON (d0.code = h.destination)
          LEFT JOIN destinations d1 ON (d1.code = d0.parent)
          WHERE %s';    //h.name = ? LIMIT 1

        $conditions =   [];

        if(isset($args['name']))
        {
            if(self::MATCH_LIKE    === $match_type)
            {
                $conditions[]  =   'h.name LIKE :name';
                $args['name']   =   '%' . $args['name'] . '%';
            }else
                $conditions[]   =   'h.name = :name';

        }
        else
            unset($args['name']);

        if(isset($args['city']) && !empty($args['city']))
            $conditions[]   =   '(d0.name = :city or d1.name  = :city)';
        else
            unset($args['city']);

        $query  =   sprintf($queryfmt, implode(' AND ', $conditions));
        $stmt   =   $this->getDB()->prepare($query);
        $exec   =   $stmt->execute($args);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::HOTELCLASS);
    }

    protected function getResponse($method, $params = [])
    {
        $url    =
            sprintf("https://%s:%s@%s%s/%s",
                urlencode($this->username),
                urlencode($this->password),
                $this->api_hostname,
                $this->api_path . $method,
                $params ? '?' . http_build_query($params) : ''
            );

        $response   =   file_get_contents($url);

        if(!$response)
        {
            $error  =   error_get_last();
            throw new Exception($error['message'], $error['type']);
        }

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