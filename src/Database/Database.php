<?php

/**
 * Created by PhpStorm.
 * User: yomek
 * Date: 9/2/16
 * Time: 8:01 PM
 */

namespace Amazo\Database;


use Amazo\Config\Config;

class Database
{
    private $pdo =  null;
    private $config;
    private $connected = false;


    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        //parse from config
        $dns = $this->config->getDbDsn();
        $username = $this->config->getDbUsername();
        $password = $this->config->getDbPassword();

        //connect
        $this->pdo = new \PDO($dns,$username,$password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
        $this->connected = true;
        return $this->pdo;
    }

    public function insert($columns,$bindings,$table,$values)
    {
        if($this->connected)
        {
            //build the query statement and execute
            $query ='INSERT INTO '.$table.'('.$columns.')VALUES('.$values.')';
            $stmt = $this->PDO->prepare($query);

            $stmt->execute($bindings);

            return $stmt->rowCount() > 0;
        }

        return $this->connectionNotEstablished();
    }

    public function select($columns,$bindings,$table,$where='')
    {
        //ensure connected
        if($this->connected)
        {
            //building the query
            if($where!=='')
            {
                $where = 'where '.$where;
            }
            $query = 'SELECT '.$columns.' FROM '.$table.' '.$where.'';
            $stmt = $this->PDO->prepare($query);


            //execute base on conditions
            if($where==='' || empty($where))
            {
                $stmt->execute();
            }

            else
            {
                $stmt->execute($bindings);
            }

            //any success result? return PDOSTATEMENT otherwise false
            if($stmt->rowCount() > 0)
            {
                return $stmt;
            }

            return false;
        }

        return $this->connectionNotEstablished();
    }

    public function delete($bindings,$table,$where)
    {
        if($this->connected)
        {
            $query = 'DELETE FROM '.$table.' Where '.$where. ' ';
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($bindings);

            return $stmt->rowCount() > 0;
        }

        return $this->connectionNotEstablished();
    }

    public function update($bindings, $table, $set,$where)
    {
        //ensure connection has established
        if ($this->connected) {
            //try execute the query statements
            $query = 'UPDATE ' . $table . ' SET ' . $set . ' WHERE ' . $where . ' ';
            $stmt = $this->PDO->prepare($query);

            //update successful return true
            $stmt->execute($bindings);

            return $stmt->rowCount() > 0;
        }

        return $this->connectionNotEstablished();
    }

    public function advanceSql($query,$bindings)
    {
        //ensure that the connection is already established
        if($this->connected)
        {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($bindings);
            return $stmt;
        }

        return $this->connectionNotEstablished();
    }









    private function connectionNotEstablished()
    {
        $obj = new \stdClass();
        $obj->status = 400;
        $obj->error = 'A database connection has not been established';

        return $obj;
    }


}