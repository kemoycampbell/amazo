<?php

/**
 * Created by PhpStorm.
 * User: yomek
 * Date: 9/2/16
 * Time: 5:57 PM
 */

namespace Amazo\Config;

class Config
{
    private $username;
    private $password;
    private $dsn;


    public function __construct($dbUsername,$dbPassword,$dbDsn)
    {
        $this->username = $dbUsername;
        $this->password = $dbPassword;
        $this->dsn = $dbUsername;
    }

    public function getDbUsername()
    {
        return $this->username;
    }

    public function getDbPassword()
    {
        return $this->password;
    }

    public function getDbDsn()
    {
        return $this->dsn;
    }


}