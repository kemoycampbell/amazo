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
use Amazo\Protection\Protection;

class Amazo
{
    private $config;
    private $database=NULL;


    public function __construct()
    {
    }

    public function setConfig($config)
    {
        if(!$config instanceof Config)
        {
            throw new \InvalidArgumentException("config must be an instance of Config");

        }

        $this->config = $config;
    }

    public function database()
    {
       if($this->database==NULL)
       {
           $this->database = new Database($this->config);
       }

       return $this->database;
    }

    public function protection()
    {
        return new Protection();
    }

}