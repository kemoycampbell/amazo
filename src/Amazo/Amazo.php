<?php

/**
 * Created by PhpStorm.
 * User: yomek
 * Date: 8/27/16
 * Time: 2:33 PM
 */

namespace Amazo;

use Amazo\Config\Config;
use Amazo\Database\Database;

class Amazo
{
    private $config;


    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function database()
    {
       return new Database($this->config);
    }

}