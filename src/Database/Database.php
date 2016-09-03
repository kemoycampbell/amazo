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
    /**
     * @var $pdo
     */
    private $pdo =  null;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var bool
     */
    private $connected = false;


    /**
     * Database constructor - Takes and initialize the constructor with the Config
     * instance. An execption is thrown if the config is not an instance of Config.
     * @param Config $config - the Config instance
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * This method taks the dsn, username and password from the Config
     * and attempt to connect to the database. If the connection was success
     * the PDO instance is return otherwise an exception is thrown
     * @return null|\PDO
     */
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

    /**
     * This funtion performs insert of data in a database and returns true
     * if the insert was successful.
     * @param $columns - the columns in the table to insert the data
     * @param $bindings - an array of binding statements
     * @param $table - the table to insert the data in
     * @param $values - the place holder of values being insert. Should be in the same order as
     *                  column parameter
     * @return bool|\stdClass - returns true if the insert was successful.
     * returned otherwise a false is return. PDOException are thrown upon database exception
     */
    public function insert($columns,$bindings,$table,$values)
    {
        if($this->connected)
        {
            //build the query statement and execute
            $query ='INSERT INTO '.$table.'('.$columns.')VALUES('.$values.')';
            $stmt = $this->pdo->prepare($query);

            $stmt->execute($bindings);

            return $stmt->rowCount() > 0;
        }

        return $this->connectionNotEstablished();
    }


    /**
     * This method performs the select function on the database. Prior to executing, the method checks to ensure
     * that the connection has been established prior to moving forward. If the connection is not established then
     * a stdClass object is returned detailing the error. The method takes the necessary parameters and performs the query
     * if it was successful a PDOStatement instance is return. If the query was successful but no match was found for the given
     * parameters then false is return. Upon an execption, a stdClass is thrown detailing the errors.
     *
     * @param $columns
     * @param $bindings
     * @param $table
     * @param $where
     * @return bool|\stdClass|\PDOStatement -returns false if no match was found, return stdClass if any Exception.
     *if a match was found, a PDOstatement is returned
     */
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

    /**
     * This method delete from the database based on the given parameters. If the data was successful removed
     * true is returned otherwise false. If an exception or errors occurs then stdClass is returned detailing the error
     * @param $bindings
     * @param $table
     * @param $where
     * @return bool|\stdClass - return true if the data was successful delete. False if nothing was deleted or stdclass
     * should any type of errors occurs
     */
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


    /**
     * This methods first checks that the connection is established then attempts to performs update based on the given parameter.
     * If the update was successful then true is returned. False is return if nothing is update. Should there be any type of errors
     * they are returned in the stdclass
     * @param $bindings
     * @param $table
     * @param $set
     * @param $where
     * @return bool|\stdClass - return true if the update was successful, false if nothing was update otherwise stdclass with details
     * of error(s) that occured.
     */
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


    /**
     * This gives the ability to performs far more advanced sql task such as including limits,unions,etc
     * @param $query - the advanced sql
     * @param $bindings
     * @return \stdClass
     */
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


    /**
     * This is a private method that is used to called connection not
     * established in desired method(s).
     * @return \stdClass - return stdClass containing the status code and error message.
     */
    private function connectionNotEstablished()
    {
        $obj = new \stdClass();
        $obj->status = 400;
        $obj->error = 'A database connection has not been established';

        return $obj;
    }


}