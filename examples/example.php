<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12/3/16
 * Time: 5:50 PM
 */

require('../src/Config/Config.php');
require('../src/Amazo/Amazo.php');
require('../src/Database/Database.php');
require('../src/Protection/Protection.php');
use Amazo\Amazo;
use Amazo\Config\Config;


//database configuration
$dsn = 'mysql:host=localhost;dbname=dvwacopy;charset=utf8';
$username = 'root';
$password = '';

$config = new Config($username,$password,$dsn);

//setting up amazo
try
{
    $amazo = new Amazo($config);

    //connect the database
    $amazo->database()->connect();
}
catch(Exception $e)
{
    //some action
}

//generated secure salt hashed password
try
{
    $nounce = "someNounce"; //nounce parameter is optional
    $password = "amazo";

    $securePassword = $amazo->protection()->generateSecurePassword($password,$nounce);

    print($securePassword);

}

catch(Exception $e)
{
    //some action
}


////try
////{
////    $table = 'users';
////    $data = $amazo->database()->select($table);
////
////    if($data instanceof PDOStatement)
////    {
////        $data = $data->fetchAll();
////        print_r($data);
////    }
////}
////catch(Exception $e)
////{
////    //some action
////}