# Web Application Development Overview
Developing web applications are tedious task. If not everything, the core codes are always the same.As developers, we want to make the best use of our time and an efficient way of doing repetitive tasks. Thus we are presented with an efficient solution namely web frameworks. Webframeworks have the following goals:

* Toolboxs - eleglant features are already made for us which we can easily called.
* Fast - Since most of the works have already been done for us, we can release our applications at more faster pace.
* Reuseable codes - web framework follows OOP principles and most codes are reuseable.

Some example of well known framework are Drupal, Django, NodeJs, Laravel and among other. They all present the same challenges:
* steep learning curves
* GUI based interaction
* heavyweight
* Lack of customized exceptions handling

Amazo is far from being perfect, in fact I believe no framework will ever be the answer to every problems. However, Amazo aims on tackle the existing problems faced in existing webframeworks and give developers more options.

# Amazo Overview
Amazo is a lightweight skeleton-like framework that assist developers in releasing and develops web applications, websites or web services at a rapid pace. Unlike other frameworks, Amazo offers developers flexibility in terms of space, efficient and usage.

# what makes Amazo different from other web framework

* Skeleton-like framework - Instead of giving developers a large version of framework and force them to learn the whole layout in order to develop a website. We provide the minimum "bones" the developers need to build a web application. All need is to call the necessary methods/functions to build the desired component.

* Easy learning curve - Amazo is a straightforward webframework. Amazo is designed to be an instinctive framework. Method calling and usages should be straightforward and do without thinking. 

* Native code interaction framework - Unlike other web framework, Amazo doesnt force developrs to build their application using GUI based interaction. But rather Amazo is a framework that allows developers to acutally write code and interact with it natvely.

* Built in secured database operations - In today's world, security have never been more important than it is today. Amazo database interactions are written in PDO using prepare statements in order to protect against sql injection and other known attacks. Developers are still required to perform their own string santiziation and validations. 

* Flexibility - We all fell in love with coding because of the ability to do anything and have it submit to our wills. Unlike another web framework, Amazo doesnt take away the flexiblity from the developers. For example, we leave all exception handlings up to the developers so they can decide what action they want to take should an exception occured. 

#Amazo Layout
![ScreenShot](https://github.com/kemoycampbell/amazo/blob/master/amazo.png)

#Assumptions and Dependicies
Vulnerabilities are often introduced in system because developers failed to take securities into consideration. Failure to fully understand the risks of improper usage of Amazo component can leads to a catastrophic system which can result in millions of dollars to clean up or repair. Amazo leaves exceptional handling up to the developers, it is assumed that the developers will
handle exceptions accordingly. It is also assumed that the developers understands that Amazo is written in PHP therefore it inherits any programming flaws or risks that is associated with the language. With that being said, the following assumptions are made:

* The minimum required version PHP 5.6 is installed
* Apache is running and accessible to the developer’s prefer port.
* MYSQL server is installed. Other type of database servers have not been tested.
* Developers handle all exceptions.
* Developer is well verses in using PHP and accept both known and unknown risks are associate with the language.
* Developers are familiar with all various type of attack against web applications and know how to protect againstthem.
* Developers are running the latest version of Amazo

#usage

###### Database usage example

```php
//Dependencies declarations
require('../src/Config/Config.php');
require('../src/Amazo/Amazo.php');
require('../src/Database/Database.php');
use Amazo\Amazo;
use Amazo\Config\Config;

//database configuration
$dsn = 'mysql:host=localhost;dbname=dvwacopy;charset=utf8';
$username = 'root';
$password = '';

$config = new Config($username,$password,$dsn);
//setting up amazo
try
{
    $amazo = new Amazo($config);

    //connect the database
    $amazo->database()->connect();
}
catch(Exception $e)
{
    //some action
}

//simple select example
try
{
    $table = 'users';
    $res = $amazo->database()->select($table);

    if($res instanceof PDOStatement)
    {
        $data = $res->fetchAll();

        print_r($data);
    }
    else if($res instanceof stdClass)
    {
        echo "attempting to use a database for which a connection havent successful established!";
        exit;
    }

}

catch(Exception $e)
{
    //some action
}

```

###### Protection example
```php
require('../src/Amazo/Amazo.php');
require('../src/Protection/Protection.php');
use Amazo\Amazo;

//generated secure salt hashed password
try
{
    $amazo = new Amazo();

    $nounce = "someNounce"; //nounce parameter is optional
    $password = "amazo";

    $securePassword = $amazo->protection()->generateSecurePassword($password,$nounce);

    print($securePassword);

}

catch(Exception $e)
{
    //some action
}
```








