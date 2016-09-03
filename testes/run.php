<?php
/**
 * Created by PhpStorm.
 * User: yomek
 * Date: 9/3/16
 * Time: 3:38 PM
 */

require_once('DatabaseTest.php');

$testes = new \Amazo\Test\DatabaseTest();

$testes->testConstructor();
$testes->testConnection();
$testes->testInsert();
$testes->testDelete();