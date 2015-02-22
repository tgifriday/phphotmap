PHP Hot Mapping

Hot Mapping of PHP data, functions and methods in JavaScript

phpHotMap is an open source library for JavaScript and PHP, that 
allow mapping the PHP functions, static methods of classes and 
arbitrary data on the spot of the instance a JavaScript class. 
With this class you can call a functions and methods via AJAX. 

For example:

--------------------------------------------
In the server:
--------------------------------------------
<?php

// Include the library 

include "phpHotMap.php";
include "phpHotMapServer.php";  

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
<script type = "text/javascript" src="phpHotMap.js"></script>
<script type = "text/javascript">

    var client = new phpHotMapClient("server.php");
    
    var sum = client.sum(20, 10);
    
    var employees = client.Enterprise.getEmployees();
    
    var firstEmployeeName = employees[0]['name'];

</script>
