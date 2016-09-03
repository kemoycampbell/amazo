<?php

/**
 * Created by PhpStorm.
 * User: yomek
 * Date: 9/2/16
 * Time: 10:38 PM
 */
namespace Amazo\Test;
use Amazo\Config\Config;
use Amazo\Database\Database;
require_once('../src/Config/Config.php');
require_once('../src/Database/Database.php');


class DatabaseTest
{
    private $database = null;
    public function testConstructor()
    {
        $config = new Config('testy','passy','foo');
        //assertInstanceOf(Config::class,$config);
    }


    public function testConnection()
    {
        $dsn='mysql:host=localhost;dbname=amazo;charset=utf8';
        $username='root';//database username
        $password='';//database password
        $config = new Config($username,$password,$dsn);
        //assertInstanceOf(Config::class,$config);


        $this->database = new Database($config);
        //assertInstanceOf(Database::class,$this->database);
        $this->database->connect();



    }

    public function testInsert()
    {
        $username = 'amazo';
        $string = 'hello I am amazo. Nice to meet you';
        $columns = 'username,string';
        $bindings = array(':username'=>$username,':string'=>$string);
        $tables = 'test';
        $values=':username,:string';

        $res = $this->database->insert($columns,$bindings,$tables,$values);


        if($res===true)
        {
            echo "Insert passed";
        }
    }

    public function testDelete()
    {
        $username = 'amazo';
        $table='test';
        $where = 'username = :username';
        $bindings = array(':username'=>$username);
        $res = $this->database->delete($bindings,$table,$where);

        if($res===true)
        {
            echo '<br/>delete passed';
        }


    }

}
