#####Note: With feedback received, the project is being updated and massive changes in the architecture is coming. You may star, watch, fork but do not submit any pull requests until I have pushed the changes. If you have suggestions please use the issues.

# Amazo Overview

As we develops web applications, we want to spend time on doing real works instead of writing tedious tasks such as prepare
statements to perform create, update and delete (CRUD) operations on the databases via PDO class, verify credentials, create
secured hashed password and among others. Amazo is a OOP utility library that aims on speeding up the overall development time by providing
methods that already take care of those for you under the hood. Thus elimating the need for you to write the common tasks repeatly 
from scratch.

In additional Amazo is the improvised version of the utility library https://github.com/kemoycampbell/comTaskLib. Unlike the previous
version Amazo is more robust, available to use on multiple databases such as postgree, mysql, etc. In Amazo we also introduced 
a more consistency way of parsing the return statements of the methods. For example, should an exception or error occurs on a
method, they are returned via std class with the following properties : status, error and method. Status always return 400, with
error stating the type of error that occurs and methods indicate the method that the error occured in.




