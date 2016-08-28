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




    
#TODO Features/Options
#
#
    
#Contributing and goal

It is my hope that this framework will:
#
1. Mature
2. Fix and patch bugs as they are discovered
3. Improve in robustness
4. Implemented Additional features
5. Remained lightweight

Amazo is an open source, community-driven project. If you'd like to contribute, feel free to fork the project, play around with it, break it, improve it and lastly but not the least, submit pull requests.



