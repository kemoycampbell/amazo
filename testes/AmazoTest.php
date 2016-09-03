<?php
/**
 * Created by PhpStorm.
 * User: yomek
 * Date: 9/3/16
 * Time: 2:12 AM
 */

namespace Amazo\Test;
use Amazo\Config\Config;
use Amazo\Amazo;
use Amazo\Database\Database;
require_once('../src/Config/Config.php');
require_once('../src/Amazo/Amazo.php');
require_once('../src/Database/Database.php');



class AmazoTest extends \PHPUnit_Framework_TestCase
{
    public function testAmazoConstructor()
    {
        //config test
        $config = new Config('testy','passy','foo');
        $this->assertInstanceOf(Config::class,$config);
        $this->assertEquals('passy',$config->getDbPassword());
        $this->assertEquals('testy',$config->getDbUsername());
        $this->assertEquals('foo',$config->getDbDsn());

        //amazo test
        $amazo = new Amazo($config);
        $this->assertInstanceOf(Amazo::class,$amazo);

    }

    public function testcallDatabaseInstanceFromAmazo()
    {
        $config = new Config('testy','passy','foo');
        $amazo = new Amazo($config);

        $this->assertInstanceOf(Database::class,$amazo->database());
    }




}
