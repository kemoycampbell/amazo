<?php
/**
 * Created by PhpStorm.
 * User: yomek
 * Date: 8/27/16
 * Time: 2:53 PM
 */
require_once('../src/Amazo/Amazo.php');
use Amazo\Amazo;

$dns='mysql:host=localhost;dbname=rhonda;charset=utf8';
$username='root';//database username
$password='';//database password


//do not need any configuration
$config = array('dns'=>$dns,'username'=>$username,'password'=>$password);

$amazo = new Amazo($config);

print_r($amazo->connect());

$amazo->getMethods();