<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/8/16
 * Time: 6:23 AM
 */

namespace Amazo\Test;


use Amazo\Config\Config;
use Amazo\Amazo;

require_once('../src/Config/Config.php');
require_once('../src/Database/Database.php');
require_once('../src/Protection/Protection.php');
require_once('../src/Amazo/Amazo.php');


//database configuration
$dsn='mysql:host=localhost;dbname=dvwacopy;charset=utf8';
$username='root';//database username
$password='';//database password
$config = new Config($username,$password,$dsn);

//call amazo framework
$amazo = new Amazo($config);

$table = 'myTable';
$data = $amazo->database()->select($table);

print_r($data->fetchAll());





$database = new Database($config);
$database->connect();

$column = '*';
$table='users';
$where = 'first_name = :first_name';
$binding = array(':first_name'=>'admin');
$data = $database->select($column,$table,$binding,$where);

foreach($data as $row)
{
    print_r($row);
    print("<br/>");
}