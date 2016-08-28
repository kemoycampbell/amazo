# Amazo Overview

As we develops web applications, we want to spend time on doing real works instead of writing repeatous tasks such as prepare
statements to perform create, update and delete (CRUD) operations on the databases via PDO class, verify credentials, create
secured hashed password and among others. Amazo is a OOP framework that aims on speeding up the overall development time by providing
methods that already take care of those for you under the hood. Thus elimating the need for you to write the common tasks repeatly 
from scratch.

In additional Amazo is the improvised version of the framework https://github.com/kemoycampbell/comTaskLib. Unlike the previous
version Amazo is more robust, available to use on multiple databases such as postgree, mysql, etc. In Amazo we also introduced 
a more consistency way of parsing the return statements of the methods. For example, should an exception or error occurs on a
method, they are returned via std class with the following properties : status, error and method. Status always return 400, with
error stating the type of error that occurs and methods indicate the method that the error occured in.

## Available Methods
 * connect()
 * validate_ip($ip)
 * get_ip_address() 
 * crfValidating($crfname)
 * enableSandbox()
 * verifyUsername($username,$username_field_in_database,$table)
 * verifyCredential($combination,$passwordField,$table,$identity,$identityField)
 * generateCRFToken()
 * generateProtection($username)
 * autoLogoutExpireSession($logoutpath)
 * enforcer($logoutPath)
 * insert($columns,$bindings,$table,$values)
 * elect($columns,$bindings,$table,$where='')
 * delete($bindings,$table,$where)
 * update($bindings, $table, $set,$where)
 * generatePassCost($timeTarget,$preferTestPassword)
 * generateSecurePasswordHash($username,$password)
 * advanceSql($query,$bindings)
 * createRandomGenerate($length,$includeSpecialChar='yes',$includeNum='yes',$includeString='yes')
 * if_contain_special_chars($string)
 
##Security
Security is more important than ever. Amazo was designed with security in mind. Our connection and modification of databases
are done using PDO/prepare statements. We also ensure that connection have been established prior to performing a CRUD operation. In additional, our methods perform validations prior to moving forwards such as parameters restrains etc. Amazo is constantly improving hence we welcome feedbacks,suggestions,inputs,criticism and how we can improve the overall security of the framework.

##Install
A composer.json will be pushed soon. til then clone via the terminal as follow:

    $ git clone --recursive https://github.com/kemoycampbell/amazo.git
    $ cd cd amazo


##Usuage
below you will find some examples of structured usuages

```php
<?php

    require_once('../src/Amazo/Amazo.php');
    use Amazo\Amazo;
    
    //database configuration
    
    //you may edit $dns to match your database type postgree,mysql and so on. see php document
    //http://php.net/manual/en/pdo.connections.php and http://php.net/manual/en/ref.pdo-mysql.connection.php for 
    //further examples
    $dns='mysql:host=localhost;dbname=dbname;charset=utf8'; 
    $username='root';//database username
    $password='';//database password


    //do not need any configuration
    $config = array('dns'=>$dns,'username'=>$username,'password'=>$password);
    
    //IllegalArgumentException is thrown here if the parameter requirement is not met
    $amazo = new Amazo($config); 
    
    //connect to the database. I allow this method to return the PDO instance for flexibility
    //and as an additional feature to those who want it
    $status = $amazo->connect();
    
    //connection failed
    if($status instanceof stdClass)
    {
        some actions when the connection failed
    }
    
    //insert example 
    $col1 = 'amazo';$col='is';$col3='neat';
    $columns = 'col1,col2,col3';
    $table = 'tablename'
    $values = ':col1,:col2,:col3';
    $bindings = array(':col1'=>$col1,':col2'=>$col2,':col3'=>$col3);
    
    $res = $amazo->insert($columns,$bindings,$table,$values);
    
    //successful
    if($res===true)
    {
        echo 'ya ya';
    }
    
    //failed
    else if($res instanceof stdClass)
    {
        your action here
    }

?>

```

##changelog
Version 2
* Rename from ComTask to Amazo as in the DC comic :-)
* Consistency return statments such as stdclass for exception and errors
* update and insert method returns true on successful execution
* ensure connection is established prior to perform CRUD operations
* Secure password hashing now use the default BCRYPT instead of a manaual generated 1 and is update to the newest standard
* Remove no longer need methods

######TODO
* write unit tests using phpUnitTest
* Add .travis.yml
* Add composer for easy installation
* finish commenting methods
* improve code consistency
* write more reliable tests
* provide some sql codes that can be play with to test the demo
    
#Contributing and goal

It is my hope that this framework will:
#
1. Mature
2. Fix and patch bugs as they are discovered
3. Improve in robustness/security
4. Implemented Additional features

Amazo is an open source, community-driven project. If you'd like to contribute, feel free to fork the project, play around with it, break it, improve it and lastly but not the least, submit pull requests.



