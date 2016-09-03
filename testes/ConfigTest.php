<?php

/**
 * Created by PhpStorm.
 * User: yomek
 * Date: 9/2/16
 * Time: 10:56 PM
 */
namespace Amazo;
use Amazo\Config\Config;
require_once('../src/Config/Config.php');

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $config = new Config('testy','passy','foo');
        $this->assertInstanceOf(Config::class,$config);
        $this->assertEquals('passy',$config->getDbPassword());
        $this->assertEquals('testy',$config->getDbUsername());
        $this->assertEquals('foo',$config->getDbDsn());
    }
}
