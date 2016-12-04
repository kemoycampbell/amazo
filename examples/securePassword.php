<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12/3/16
 * Time: 5:50 PM
 */

require('../src/Amazo/Amazo.php');
require('../src/Protection/Protection.php');
use Amazo\Amazo;




//generated secure salt hashed password
try
{
    $amazo = new Amazo();

    $nounce = "someNounce"; //nounce parameter is optional
    $password = "amazo";

    $securePassword = $amazo->protection()->generateSecurePassword($password,$nounce);

    print($securePassword);

}

catch(Exception $e)
{
    //some action
}