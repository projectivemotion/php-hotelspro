<?php
/**
 * Project: PHPHotelsPro
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

namespace projectivemotion\HotelsPro;


class WebUi
{
    public static function findSessionLogin($params)
    {
        session_start() || die('eession start error');

        $show_login =   false;
        if($params['action']    ==  'logout')
        {
            session_destroy();
            $show_login =   true;
        }

        if(!isset($_SESSION['username']) || $show_login)
        {
            if($params['action']    ==  'login')
            {
                $_SESSION['username']    =   $params['arguments']['username'];
                $_SESSION['password']    =   $params['arguments']['password'];
            }else{
                self::printLoginPage();
                return NULL;
            }
        }

        return [ $_SESSION['username'], $_SESSION['password'] ];
    }

    public static function Run($username = '', $password = '', \PDO $pdo)
    {

        $params =   [
            'pageresults' => '',
            'action'    => $_GET['action'],
            'arguments' =>  $_POST
        ];

        if(empty($params['arguments']['currency']))
            $params['arguments']['currency']        =   'EUR';
        if(empty($params['arguments']['adults']))
            $params['arguments']['adults']        =   '2';

        if($username == '')
        {
            $login  =   self::findSessionLogin($params);
            if(!$login) return;
            list($username, $password)  =   $login;
        }

        $Client =   new Client($username, $password);
        $Client->setDB($pdo);

        $args   =   $params['arguments'];


        switch($params['action'])
        {
            case 'getPrice':
                $result =   [];
                $hotels  =   $Client->findHotelsBy([
                    'name'  =>  $params['arguments']['hotelname'],
                    'city'  =>  $params['arguments']['city']
                ]);
                if(count($hotels) > 1)
                {
                    $result['error']    =   'Hotel name/city returned more than 1 hotel.';
                    $result['hotels']   =   $hotels;
                }else{
                    $Query  =   new Query();
                    $Query->setHotelCode($hotels[0]->code);
                    $Query->setCheckin($args['checkin']);
                    $Query->setCheckout($args['checkout']);
                    $Query->setPax($args['adults']);
                    $Query->setClientNationality('nl');
                    $Query->setCurrency($args['currency']);
                    $Query->setLimit(10);
                    try {
                        $result = $Client->Search($Query);
                    }catch(Exception $e)
                    {
                        $result =   ['message' => 'An error ocurred.', 'info' => $e ];
                    }
                }
                $params['pageresults']  =   ['message' => 'Recieved Response from HotelsPro', 'Response' => $result];
                break;

            case 'findHotel':
                $params['pageresults']    =   $Client->findHotelsBy([
                    'name'  =>  $params['arguments']['hotelname'],
                    'city'  =>  $params['arguments']['city']
                ]);
                break;
            default:
                $result =   [ 'mode' => 'Displaying a few random hotels for you..' ];
                $result['hotels']   =   $Client->queryDB('SELECT * FROM hotels order by RANDOM() LIMIT 30');
                $params['pageresults']  =   $result;
        }

        self::printPage($params);
    }

    public static function printPage($params)
    {
        $args   =   $params['arguments'];

        ?>
        <html>
        <head><style>
                label { display: block; }
            </style></head>
        <body>

        <form action="?action=logout" method="POST">
            <input type="submit" value="Logout (Reset Credentials.)" />
        </form>

        <form action="?" method="GET">
            <input type="submit" value="Clear Search" />
        </form>

        <h2>Get Price</h2>
        <form action="?action=getPrice" method="POST">
            <label>Checkin (YYYY-MM-DD): <input type="text" name="checkin" value="<?=
                htmlentities( $args['checkin'], ENT_QUOTES); ?>"/></label>
            <label>Checkout (YYYY-MM-DD): <input type="text" name="checkout" value="<?=
                htmlentities( $args['checkout'] , ENT_QUOTES); ?>"/></label>
            <label>Hotel Name: <input type="text" name="hotelname" value="<?=
                htmlentities( $args['hotelname'], ENT_QUOTES); ?>"/></label>
            <label>City: <input type="text" name="city" value="<?=
                htmlentities( $args['city'], ENT_QUOTES); ?>"/></label>
            <label>Adults: <input type="text" name="adults" value="<?=
                htmlentities( $args['adults'], ENT_QUOTES); ?>"/></label>
            <label>Currency: <input type="text" name="currency" value="<?=
                htmlentities( $args['currency'], ENT_QUOTES); ?>"/></label>
            <label><input type="submit" value="Get Price"/></label>
        </form>

        <h2>Find Hotel</h2>
        <form action="?action=findHotel" method="POST">
            <label>Hotel Name: <input type="text" name="hotelname" value="<?=
                htmlentities( $args['hotelname'], ENT_QUOTES); ?>"/></label>
            <label>City (Can be empty.):
                <input type="text" name="city" value="<?=
                htmlentities( $args['city'], ENT_QUOTES); ?>"/></label>
            <label><input type="submit" value="Find Hotel"/></label>
        </form>

<pre>
<?php print_r($params['pageresults']); ?>
</pre>

        </body>
        </html>
        <?php

    }

    public static function printLoginPage()
    {
        ?><html>
        <body>
        <h2>HotelsPro Api Test:</h2>
        <p>You must have an existing hotelspro.sqlite database in current working directory (where you called this script from.);</p>
        <p>Please provide api authentication credentials to test client library configuration.</p>

        <br />
        <form action="?action=login" method="POST">
            <label>Username: <input name="username" /></label>
            <label>Password: <input name="password" type="password" /></label>
            <input type="submit" value="Login" />
        </form>

        <p>
            You must have created hotelspro.sqlite
        </p>
        </body>
        </html>
            <?php
    }
}