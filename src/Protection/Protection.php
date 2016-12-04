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
    private $database;


    /**
     * * Protection constructor -this create a protection constructor and return the instance.
     * The constructor checks if the session has started, if not it automatically start it
     * @param Database $database - take the instance of Database
     */
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }


    }

   public function setDatabase($database)
   {
       if(!$database instanceof Database)
       {
           throw new \InvalidArgumentException("database must be an instance of Database");
       }

       $this->database = $database;
   }

    private function validate_secure_create_user_account($table,$username,$password,$usernameCol,$passwordCol)
    {
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
    public function secureCreateUserAccount($table,$username,$password,$usernameCol,$passwordCol)
    {
        $this->validate_secure_create_user_account($table,$username,$password,$usernameCol,$passwordCol);

        if($this->database instanceof Database)
        {
            //the username acts as nounce in this case
            $password = $this->generateSecurePassword($username,$password);

            $columns = $usernameCol.','.$passwordCol;
            $bindings = array(':username'=>$username,':password'=>$password);
            $values = ':username,:password';
            $insert = $this->database->insert($columns,$bindings,$table,$values);

            return $insert;
        }

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
    public function generateSecurePassword($password,$nounce="",$options = array('cost'=>12))
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

    /**
     * This method is use to login the user in and set up the session. The method takes
     * username, password, user account table, the columns such as username and password columns,
     * where clause eg $where = 'username = :user and password = :pass" as well as bindings which
     * is an array that binds the place holders example $bindings = array(':pass'=>$password,':user'=>$username)
     *
     * @param $username
     * @param $password
     * @param $table
     * @param $columns
     * @param $where
     * @param $bindings
     * @return bool - return true user successful logged in, false if incorrect.
     *
     *Exception are thrown if the argument parameters are missing or empty. Exceptions are also
     * thrown if any issue occured with the sql statement
     */
    public function login($username,$password,$table,$columns,$where,$bindings)
    {


        if($this->isEmpty($username) || $this->isEmpty($password) || $this->isEmpty($table)||
            $this->isEmpty($columns) || $this->isEmpty($where) || $this->isEmpty($bindings))
        {
            throw new \InvalidArgumentException("all parameters are required!");
        }

        if($this->database instanceof Database)
        {

            $res = $this->database->select($table,$columns,$where,$bindings);
            if($res instanceof \PDOStatement)
            {
                $_SESSION['ip_address'] = $this->get_ip_address();
                $_SESSION['verified'] = true;
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                $this->associateLoginData = $res->fetch();
                return true;
            }
        }

        return false;
    }

    /**
     * This method is used to fetch the user's ip
     * @return bool|string return the ip otherwise return false upon failure to fetch ip
     */
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


    /**This method is used to secure page for only authorized users.
     *
     * This method takes the logout, register, and login path then checks if the user is authorized to view a page.
     * If the user is not logged in, he/she will be redirected to the login page. If the user session has changed since
     * he/she last logged in, they will be redirect to the login page.
     *
     * Sessions that is checked for:
     * ip address, user_agent, username, verified
     *
     * The sessions are set by the login function
     * @param $logoutPath
     * @param $register
     * @param $login
     */
    public function auth($logoutPath,$register,$login)
    {
        //get the current page
        $basename = basename($_SERVER["SCRIPT_FILENAME"], '.php');

        if($this->isEmpty($logoutPath) || $this->isEmpty($register) || $this->isEmpty($login))
        {
            throw new \InvalidArgumentException("all parameters are required");
        }

        if(!isset($_SESSION['username']) || !isset($_SESSION['verified']))
        {
            if($basename!==$register || $basename!==$login)
            {
                $this->logout($logoutPath);
            }
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

    /**
     * This method is used to logout the user and destroy all sessions
     * @param $path - the path to redirect the user upon successful logout.
     */
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