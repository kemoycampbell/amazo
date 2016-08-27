<?php

/**
 * Created by PhpStorm.
 * User: yomek
 * Date: 8/27/16
 * Time: 2:33 PM
 */

namespace Amazo;

class Amazo
{
    private $username = null;
    private $password = null;
    private $dns = null;
    private $maxConfigParameters = 3;
    private $PDO = null;
    private $connected = false;
    private $sandbox = false;
    private $ip= null;

    /**
     * Amazo constructor take an array supplied
     * with the database configuration data. Configuration data
     * must included information such as dns, username and password
     *
     * @param $config - The array containing the configuration data.
     *                  The parameters required in the array are:
     *                  2. username
     *                  3. password
     *
     * Failure to supplied the required parameters will throw an error
     * and the program will terminate
     */

    public function __construct($config)
    {
        //ensure that config is an array
        if(!isset($config)|| !is_array($config))
        {
            $error = 'Bad configuration. The config parameter must be passed as array';
            $method = '__construct';
            $output = json_encode($this->stdErr($error,$method));

            throw new \InvalidArgumentException($output);
        }

        //ensure the required parameters are supplied
        //we want to ensure that the array contain 3 parameters namely dns, username and password




        else if(count($config) > $this->maxConfigParameters || !isset($config['dns']) || !isset($config['username'])
              || !isset($config['password']))
        {
            $error = 'Bad parameter(s)! The config parameter must be passed
             as follow array(\'username\'=>$username,\'password\'=>$password). In additional, the variables cannot be null';
            $method = '__construct';

            $output = json_encode($this->stdErr($error,$method));

            throw new \InvalidArgumentException($output);
        }

        $this->dns = $config['dns'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    public function connect()
    {

        //attempt to connect to the database with the supplies parameters
        //that was supplied in the constructor. If success enable connected as true
        //as well as return the PDO instance.
        try
        {
            $this->PDO = new \PDO($this->dns,$this->username,$this->password);
            $this->PDO->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
            $this->connected = true;
            return $this->PDO;
        }


        //an error occured hence we will return a std class detailing the error
        catch(\PDOException $e)
        {
            return $this->stdErr($e->getMessage(),'connect');
        }
    }


    /**
     * This method is used to enable the sanbox mode. This is
     * recommended if the developer is on a localhost.
     */
    public function enableSandbox()
    {
        $this->sandbox = true;

        //set the ip address as localhost
        $this->ip = '127.0.0.1';
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


    /**
     * This funtion performs insert of data in a database and returns true
     * if the insert was successful. If an exception occured then a stdClass is
     * returned otherwise a false is return.
     * @param $columns - the columns in the table to insert the data
     * @param $bindings - an array of binding statements
     * @param $table - the table to insert the data in
     * @param $values - the place holder of values being insert. Should be in the same order as
     *                  column parameter
     * @return bool|\stdClass - returns true if the insert was successful. If an exception occured then a stdClass is
     * returned otherwise a false is return.
     */
    public function insert($columns,$bindings,$table,$values)
    {
        //ensure connection is established
        if($this->connected)
        {
            //build the query statement
            $query ='INSERT INTO '.$table.'('.$columns.')VALUES('.$values.')';
            $stmt = $this->PDO->prepare($query);

            //if our action was successful return true
            try
            {
                $stmt->execute($bindings);

                if($stmt->rowCount() > 0)
                {
                    return true;
                }
                return false;

            }

            //an error occurs hence return the stdclass with detailed errors
            catch(\PDOException $e)
            {

                return $this->stdErr($e->getMessage(),'insert');
            }
        }

        //connection is not established
        else
        {
            return $this->connectionNotEstablished();
        }
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
        //ensure database is already connected
        if($this->connected)
        {

            //building the query
            if($where!='')
                $where = 'where '.$where;
            $query = 'SELECT '.$columns.' FROM '.$table.' '.$where.'';
            $stmt = $this->PDO->prepare($query);

            //attempt to perform the action
            try
            {
                //call the execute method depends on whether we have a where clause
                if($where==='' || empty($where))
                {
                    $stmt->execute();
                }
                else
                {
                    $stmt->execute($bindings);
                }

                //did we successful fetch the data? if yes return
                //$stmt which is a PDOStatement instance
                if($stmt->rowCount() > 0)
                    return $stmt;

                //otherwise return false as no match was found
                return false;
            }
            catch(\PDOException $e)
            {
                return $this->stdErr($e->getMessage(),'select');
            }

        }

        else
        {
            return $this->connectionNotEstablished();
        }
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

        //ensure that the connection has first been established
        if($this->connected)
        {

            //try to execute the delete
            $query = 'DELETE FROM '.$table.' Where '.$where. ' ';
            $stmt = $this->PDO->prepare($query);
            try
            {
                $stmt->execute($bindings);
                if($stmt->rowCount() > 0)
                    return true;

                //nothing to delete
                return false;
            }

                //exception
            catch(\PDOException $e)
            {
               return $this->stdErr($e->getMessage(), 'delete');
            }
        }

        //no connection return connection status
        else{
            return $this->connectionNotEstablished();
        }


    }//end of delete

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
        if($this->connected)
        {
            //try execute the query statements
            $query = 'UPDATE '.$table.' SET '.$set.' WHERE '.$where. ' ';
            $stmt = $this->PDO->prepare($query);

            try
            {
                //update successful return true
                $stmt->execute($bindings);
                if($stmt->rowCount() > 0)
                    return true;

                //nothing to update return false
                return false;
            }

                //execption handling
            catch(\PDOException $e)
            {
                return $this->stdErr($e->getMessage(),'update');
            }

        }

        //no connection, return connection status
        else
        {
            return $this->connectionNotEstablished();
        }

    }

    /**
     * This method is used to generate  to generate the best cost for password hashing
     * on the Bcrypt algorithm. The method returns the best cost otherwise a stdclass with the errors
     *
     * @param $timeTarget
     * @param $preferTestPassword
     * @return int|\stdClass - return the best cost otherwise stdclass with detailed error(s)
     */
    public function generatePassCost($timeTarget,$preferTestPassword)
    {
        //ensure that targetTIme or preferredPassword is not null
        if(empty($timeTarget) || empty($preferTestPassword) || $timeTarget=='' || $preferTestPassword='' )
        {
            return $this->stdErr('All parameters are required!',__METHOD__);
        }

        else
        {
            $cost = 8;
            do
            {
                $cost++;
                $start = microtime(true);
                password_hash($preferTestPassword, PASSWORD_BCRYPT, ["cost" => $cost]);
                $end = microtime(true);
            }while (($end - $start) < $timeTarget);

            return $cost;
        }

    }

    /**
     * This methods takes a string and check and checks whether it contain special character
     * @param $string - the string to check for special character
     * @return bool - return true if contain special character otherwise false
     */
    public function if_contain_special_chars($string)
    {
        if (!preg_match('/[^A-Za-z0-9]/', $string)) // '/[^a-z\d]/i' should also work.
        {
            //does not contain special chars
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * This method uses the BCRYPT and the secured random salt to generated a secured hashed password
     * Under the hood, it uses the password_hash($username.$password, PASSWORD_BCRYPT,option) function.
     * Should there be an error, the stdclass is returned otherwise the secured generated password is returned
     * @param $username
     * @param $password
     * @return bool|\stdClass|string - returns false if password_hash failed,stdclass if there are any errors othewise the
     * the hashed password is returned
     */
    public function generateSecurePasswordHash($username,$password)
    {
        //ensuring that both username and password is not empty
        if(empty($username) || empty($password) || trim($username)=='' || trim($password)=='')
        {
            $error = 'All parameters are required';
            $method = __METHOD__;

            return $this->stdErr($error,$method);

        }

        else
        {
            //automatically generate the cost
            $cost = $this->generatePassCost(0.05,$username.$password);
            $options = array('cost'=>$cost);
            $res = password_hash($username.$password, PASSWORD_BCRYPT, $options);

            //hide the algorithm string
            //$res = substr($res,7,strlen($res));
            return $res;
        }

    }//end of generating hash function

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
            $stmt = $this->PDO->prepare($query);

            //try execute the statement
            try{
                $stmt->execute($bindings);
                return $stmt;
            }
            catch(\PDOException $e)
            {
                $error = $e->getMessage();
                $method = __METHOD__;
                return $this->stdErr($error,$method);
            }
        }

        else{
            return $this->connectionNotEstablished();
        }
    }

    public function createRandomGenerate($length,$includeSpecialChar,$includeNum,$includeString)
    {
        $randomGenerated=null;
        $char=null;

        //different things that can be generated
        $string  ='ABCDEFGHIJKLMNOPQRSTUVWXYZabdefghijklmnopqrstuvwxyz';
        $num = '0123456789';
        $specialCharacter = '@#$&!,.?~-_*';


        //ensure that $length is numeric
        if(!is_numeric($length))
        {
            $obj = new \stdClass();
            $obj->status = 400;
            $obj->error = 'The parameter length must be an int';

            return $obj;
        }

        //ensure the user do not leave specialCharacter or num blank
        if($includeNum=="" || $includeSpecialChar=="" || $includeString=="")
        {
            $obj = new \stdClass();
            $obj->status = 400;
            $obj->error = 'The parameter $includeSpecialChar or $includeNum or $includeString cannot be blank.';

            return $obj;
        }

        else
        {
            //the user wish to add strings
            if( strcasecmp ( $includeString , 'yes' )==0 )
            {
                $char.=$string;
            }

            //the user wish to add numbers
            if( strcasecmp ( $includeNum , 'yes' )==0 )
            {
                $char.=$num;
            }

            //the user wish to add special characters
            if( strcasecmp ( $includeSpecialChar , 'yes' )==0 )
            {
                $char.=$specialCharacter;
            }

            //begin the randomize code
            srand((double)microtime()*1000000);
            $i=1;
            while($i<=$length)
            {
                $randNum = rand() % strlen($char);//random between the length of the $char
                $temp = substr($char,$randNum,1);
                //temp fix...prevent temp from adding blank
                if($temp!="")
                {
                    $randomGenerated.=$temp;
                    $i++;

                }
            }
            return $randomGenerated;
        }
    }//end of random generated function










    private function stdErr($error,$method)
    {
        $obj = new \stdClass();
        $obj->status = 400;
        $obj->error = $error;
        $obj->method = $method;

        return $obj;
    }

}