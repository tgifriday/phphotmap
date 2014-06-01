* PHP Hot Mapping
* Hot Mapping of PHP data, functions and methods in JavaScript
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation.
*
* This program is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
* for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program as the file LICENSE.txt; if not, please see
* http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt.
*
*
* @author Rafael Rodriguez Ramirez
*
* @email rrodriguezramirez@gmail.com
*
* @url http:://phphotmap.salvipascual.com
*
* @version 1.0

_______________________________________________________________

What is phpHotMap?
_______________________________________________________________

phpHotMap is an open source library for JavaScript and PHP, that 
allow mapping the PHP functions, static methods of classes and 
arbitrary data on the spot of the instance a JavaScript class. 
With this class you can call a functions and methods via AJAX. 
This library are compose of two files:

    * phpHotMap.php, for build and publish the mapping in the 
      server side
    * phpHotMap.js, for obtain the mapping and execute the 
      remote methods in the client side.

For example:

--------------------------------------------
In the server:
--------------------------------------------
<?php

// Include the library 

include "phpHotMap.php"; 

// The program 
// Note: the programa can be located in an external file 

function sum($x, $y){
  return $x + $y; 
}

class Enterprise{
  public function getEmployees(){
      return array(
        array("name" => "Thomas Hardy", "salary" => 1500),  
        array("name" => "Christina Berglund", "salary" => 1200)  
      );  
    } 
} 

// Server instance ...

$server = new phpHotMaServer(); 

// ... Add methods ...

$server->addMethod("sum", "x,y"); 
$server->addMethod("Enterprise::getEmployees", "x,y"); 

// ... and go!
$server->go(); 

?>
--------------------------------------------
In the client:
--------------------------------------------

<script type = "text/javascript">

    var client = new phpHotMapClient("server.php");
    
    var sum = client.sum(20, 10);
    
    var employees = client.Enterprise.getEmployees();
    
    var firstEmployeeName = employees[0]['name'];

</script>

______________________________________________________________

How to use phpHotMap?
______________________________________________________________

phpHotMap can be use in two steps.

    * The first step is publish the functions, methods and data 
    in the server side through instance of the phpHotMapServer class 
    located in the phpHotMap.php file. You can also implement 
    some util hooks. 

    * The second step is the use of phpHotMap.js  in the web page. 
    You can create an instance of phpHotMapClient  and pass in the 
    constructor the URL address of server where is located the 
    publication elaborated in the first step. 

______________________________________________________________

Workflow
______________________________________________________________
The follow steps are the main phpHotMap workflow:

    * [client] Instance phpHotMapClient
    * [server] Instance phpHotMapServer
    * [server] Build and publish the mapping
    * [client] Login for private methods
    * [server] Instance phpHotMapServer
    * [server] Invoke authorization hook
    * [client] Execute a method
    * [server] Instance phpHotMapServer
    * [server] Invoke check method access hook
    * [server] Invoke before hook
    * [server] Execute the method
    * [server] Send result
    * [server] Invoke after hook
    * [client] Retrieve result

______________________________________________________________

References
______________________________________________________________

----------------------
PHP Classes
----------------------
phpHotMap - Static class with some util methods.
    - checkMethodAccess($method): Check if current user can access 
    -                             to specific method.
    - checkRangeIP($from, $to, $ip): Check IP address in IP range.
    - getClientIPAddress(): Return the client IP address.
    - getHTMLOf($mixed): Return a representative HTML of data.
    - isArrayOfObject($arr): Return TRUE if $arr is an array of objects.
    - isArrayOfArray($arr): Return TRUE if $arr is an array of arrays.
    - isNumericList($arr): Return TRUE if $arr is an array of numbers.
    - login($username, $password): Check user's credencials and 
                                   start the session on server.
    - logout(): End session on server.
    - verifyAuth(): Verify authentication.   

phpHotMapServer - Server mapping
	_ phpHotMapServer($server_name): Create an instance of phpHotMapServer 
								     with $server_name as name of the server.
    - addData($var, $value): Add a variable to map.
    - addMethod($name, $params, $params_complex, $security, 
                $hosts, $description): Add a method to map.
                
                $name - The method name. For static methos use the following
                        sintax: classname::methodname,
              
                $params - Array of parameters's name. It can be a string 
                        with name separated by comas.
                
                $security - TRUE for enable the security for method.
                
                $hosts - Array of hosts's IP address that can access to server.

                $description - Description of method.
    - showFace() - Show a simple interface that show the server mapping.
    - go() - Mapping or execute. Waiting for client call.

----------------------     
Javascript Classes
----------------------

phpHotMap - Namespace of some methods.
	- getXMLHttpRequestObject(): Return a XMLHTTPRequest JS object.
	- ajax(params): Send an AJAX request and return the result as JSON.
	- call(params): Call to a method on server.
	- login(server, username, password): Login on server with specific user 
	  name and password.
	- logout(): Logout from server.

phpHotMapClient - Client side
	- Create a representative Javascript Object with some properties and 
	  methods based on mapping.
----------------------
PHP Hooks
----------------------

A hook is a function that intercept the workflow of phpHotMap in one 
point. The following hooks can be implement:

    * phpHotMap_before($method): Before execute a method.
    * phpHotMap_after($method): After execute a method.
    * phpHotMap_chkma($user, $method): Check method access for a user.
    * phpHotMap_auth($user, $password): Login on server.

----------------------
Constants
----------------------

This library contains some constants that can be returned by methods.

    * PHP_HOT_MAP_ACCESS_DENIED_HOST: Access denied for a client host.
    * PHP_HOT_MAP_ACCESS_DENIED_USER: Access denied for a user.
    * PHP_HOT_MAP_LOGIN_SUCCESSFUL:Login successful for a user.
    * PHP_HOT_MAP_LOGIN_FAILED: Login failed for a user.
    * PHP_HOT_MAP_LOGOUT_SUCCESSFUL: Logout successful for a user.
    * PHP_HOT_MAP_METHOD_EXECUTED: Method execute successful.
    * PHP_HOT_MAP_METHOD_NOT_EXISTS: Method not exists.
    * PHP_HOT_MAP_METHOD_STOPPED: The method was stopped.
    * PHP_HOT_MAP_MAPPING_SUCCESSFUL: Mapping successful.
    * PHP_HOT_MAP_INTERFACE_SHOW_SUCCESSFUL: Interface are showed successful.

______________________________________________________________

Contact
______________________________________________________________

Author: Rafael Rodriguez Ramirez
Email: rrodriguezramirez@gmail.com
Phone: +5352458998
