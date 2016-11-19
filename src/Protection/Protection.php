<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/15/16
 * Time: 5:33 PM
 */

namespace Amazo\Protection;


class Protection
{
    private $sandbox = false;
    private $ip = '127.0.0.1';

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }
    }

    public function secureCreateUserAccount($pdo,$table,$username,$password,$usernameCol,$passwordCol)
    {
        $query = "INSERT INTO $table ($usernameCol,$passwordCol) VALUES(:username,:password)";
        $stmt = $pdo->prepare($query);

        //replace the password with the hashed one
        $password = $this->generateSecurePassword($username,$password);

        $stmt->execute(array(':username'=>$username,':password'=>$password));


        //return whether the account was successful created
        return $stmt->rowCount() > 0;
    }

    public function enableSandbox()
    {
        $this->sandbox = true;
        $this->ip = '127.0.0.1';
    }

    public function generateSecurePassword($nounce,$password,$options = array('cost'=>12))
    {
        $combination = $nounce.$password;
        return password_hash($combination,PASSWORD_BCRYPT,$options);
    }

    public function trimIP($ip)
    {
        $pos = strrpos($ip,'.');
        if($pos!==false)
        {
            $ip = substr($ip, 0, $pos+1);
        }

        return $ip.'.0';
    }

    function validate_ip($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        return true;
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

        session_destroy();

        //prevent path traversal
        $path = basename(realpath($path));

        header('Location: '.$path);
        exit;
    }

}