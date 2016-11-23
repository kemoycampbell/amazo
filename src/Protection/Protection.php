<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/15/16
 * Time: 5:33 PM
 */

namespace Amazo\Protection;


use Amazo\Database\Database;

class Protection
{
    private $sandbox = false;
    private $ip = '127.0.0.1';
    private $associateLoginData = null;

    /**
     * Protection constructor -this create a protection constructor and return the instance.
     * The constructor checks if the session has started, if not it automatically start it
     */
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }
    }

    private function validate_secure_create_user_account($database,$table,$username,$password,$usernameCol,$passwordCol)
    {
        if($this->isEmpty($database))
        {
            throw new \InvalidArgumentException("database parameter cannot be empty");
        }
        if($this->isEmpty($table))
        {
            throw new \InvalidArgumentException("table parameter cannot be empty");
        }
        if($this->isEmpty($username))
        {
            throw new \InvalidArgumentException("username parameter cannot be empty");

        }

        if($this->isEmpty($usernameCol))
        {
            throw new \InvalidArgumentException("usernamecol parameter cannot be empty");
        }

        if($this->isEmpty($password))
        {
            throw new \InvalidArgumentException("password parameter cannot be empty");
        }

        if($this->isEmpty($passwordCol))
        {
            throw new \InvalidArgumentException("passwordcol parameter cannot be empty");
        }

        if(!$database instanceof Database)
        {
            throw new \InvalidArgumentException("$database must be an instance of Database");
        }
    }

    /**
     * @param $database
     * @param $table
     * @param $username
     * @param $password
     * @param $usernameCol
     * @param $passwordCol
     * @return bool|\stdClass
     */
    public function secureCreateUserAccount($database,$table,$username,$password,$usernameCol,$passwordCol)
    {
        $this->validate_secure_create_user_account($database,$table,$username,$password,$usernameCol,$passwordCol);

        if($database instanceof Database)
        {
            //the username acts as nounce in this case
            $password = $this->generateSecurePassword($username,$password);

            $columns = $usernameCol.','.$passwordCol;
            $bindings = array(':username'=>$username,':password'=>$password);
            $values = ':username,:password';
            $insert = $database->insert($columns,$bindings,$table,$values);

            return $insert;
        }

        //for any reason
        return false;
    }

    /**
     * This method is use to enable the sandbox.
     * The ip is also set to localhost 127.0.0.1
     */
    public function enableSandbox()
    {
        $this->sandbox = true;
        $this->ip = '127.0.0.1';
    }

    /**
     * Take an object and return a boolean expression whether it is empty or not.
     * Capable of checkng string, nullable, array
     * @param $object - the object to check for empty
     * @return bool - true if the object is empty and false otherwise
     */
    public function isEmpty($object)
    {
        if(!isset($object))
            return true;

        if($object==null)
            return true;

        if(strlen(trim($object))<=0)
            return true;

        if(is_array($object))
        {
            if(count($object)<=0)
                return true;
        }

        return false;
    }

    /**
     * This method is use to generate a cryptographically secured password. It takes a nounce, password(plaintext) and options
     * then use the php password_hash function along with PASSWORD_BCRYPT to returned a secured hash.
     * An InvalidArgumentException is thrown if password is empty or options is not an array
     * @param string $nounce - the nounce, username can be use for this
     * @param $password - the user's plaintext password
     * @param array $options
     * @return bool|string return a string of hashed password if succeed False if failed.
     */
    public function generateSecurePassword($nounce="",$password,$options = array('cost'=>12))
    {
        if($this->isEmpty($password))
        {
            throw new \InvalidArgumentException("Password cannot empty!");
        }

        if(!is_array($options))
        {
            throw new \InvalidArgumentException("options must be an array!");
        }

        $combination = $nounce.$password;
        return password_hash($combination,PASSWORD_BCRYPT,$options);
    }


    /**
     * This method is used to trim the ip into its appropriate format.
     * Throw an InvalidArgumentException if the ip is empty
     * @param $ip - the ip in string format
     * @return string - returns a string format of the trimmed ip
     */
    public function trimIP($ip)
    {
        if($this->isEmpty($ip))
        {
            throw new \InvalidArgumentException("ip cannot be empty");
        }

        $pos = strrpos($ip,'.');
        if($pos!==false)
        {
            $ip = substr($ip, 0, $pos+1);
        }

        return $ip.'.0';
    }

    /**
     * This method takes a string of ip and returns whether it is validate or not.
     * Throw an InvalidArgumentException ip is empty
     * @param $ip
     * @return bool
     */
    public function validate_ip($ip)
    {
        if($this->isEmpty($ip))
        {
            throw new \InvalidArgumentException("ip cannot be empty!");
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        return true;
    }

    /**
     * This method returns the data of the user that is successful login from the database.
     * Call this method after the isCredentialValid method has return true
     * @return return the associated data otherwise return null
     */
    public function getSuccessfulLoginData()
    {
        return $this->associateLoginData;
    }
    public function isCredentialValid($database,$username,$password,$table,$columns,$where,$bindings)
    {
        if(!isset($database) || !$database instanceof Database)
        {
            throw new \InvalidArgumentException("databasse parameter must be an instance of Database!");
        }

        if($this->isEmpty($username) || $this->isEmpty($password) || $this->isEmpty($table)||
            $this->isEmpty($columns) || $this->isEmpty($where) || $this->isEmpty($bindings))
        {
            throw new \InvalidArgumentException("all parameters are required!");
        }

        if($database instanceof Database)
        {
            $res = $database->select($table,$columns,$where,$bindings);
            if($res instanceof \PDOStatement)
            {

                $this->associateLoginData = $res->fetch();
                return true;
            }
        }

        return false;
    }

    public function get_ip_address()
    {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        foreach ($ip_keys as $key)
        {
            if (array_key_exists($key, $_SERVER) === true)
            {
                foreach (explode(',', $_SERVER[$key]) as $ip)
                {
                    // trim for safety measures
                    $ip = $this->trimIP($ip);
                    // attempt to validate IP
                    if ($this->validate_ip($ip))
                    {
                        return $ip;
                    }
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
    }

    public function enforcer($logoutPath)
    {
        if($this->isEmpty($logoutPath))
        {
            throw new \InvalidArgumentException("logout path cannot be empty");
        }

        if($this->sandbox==false)
        {
            $this->ip = $this->get_ip_address();
        }

        //invalid ip type
        if($this->ip===false)
        {
           $this->logout($logoutPath);
        }

        //the user ip changed or not set
        if(!isset($_SESSION['ip_address']) || $_SESSION['ip_address']!==$this->ip)
        {
            $this->logout($logoutPath);
        }

        //usergent
        if (!isset($_SERVER['HTTP_USER_AGENT']) || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'])
        {
            $this->logout($logoutPath);
        }
    }

    public function logout($path)
    {
        if($this->isEmpty($path))
        {
            throw new \InvalidArgumentException("path cannot be empty");
        }
        session_destroy();

        //prevent path traversal
        $path = basename(realpath($path));

        header('Location: '.$path);
        exit;
    }

}