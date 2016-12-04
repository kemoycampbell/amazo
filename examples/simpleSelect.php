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
use Amazo\Amazo;
use Amazo\Config\Config;


//database configuration
$dsn = 'mysql:host=localhost;dbname=dvwacopy;charset=utf8';
$username = 'root';
$password = '';

$config = new Config($username,$password,$dsn);


//using amazo
try
{
    $amazo = new Amazo();
    $amazo->setConfig($config);
    $amazo->database()->connect(); //must establish connection before database related tasks

    //simple select example
    $table = 'users';
    $res = $amazo->database()->select($table);

    if($res instanceof PDOStatement)
    {
        $data = $res->fetchAll();

        print_r($data);
    }

    //stdClass is thrown for database-related operation which we have not first established connection with the database
    else if($res instanceof stdClass)
    {
        echo "attempting to use a database for which a connection havent successful established!";
        exit;
    }

}

//for the sake of simplicity catch every exception. maybe customized to catch specific exceptions and handle
//them differently
catch(Exception $e)
{
    //do something
}