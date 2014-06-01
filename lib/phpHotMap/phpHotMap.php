<?php
/**
 * PHP Hot Mapping
 * Hot Mapping of PHP data, functions and methods in JavaScript
 *
 * Server side
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
 * @author Rafael Rodriguez Ramirez
 * @email rrodriguezramirez@gmail.com
 * @url http:://phphotmap.salvipascual.com
 * @version 1.0
 *
 */

define("PHP_HOT_MAP_ACCESS_DENIED_HOST", "PHP_HOT_MAP_ACCESS_DENIED_HOST");
define("PHP_HOT_MAP_ACCESS_DENIED_USER", "PHP_HOT_MAP_ACCESS_DENIED_USER");
define("PHP_HOT_MAP_LOGIN_SUCCESSFUL", "PHP_HOT_MAP_LOGIN_SUCCESSFUL");
define("PHP_HOT_MAP_LOGIN_FAILED", "PHP_HOT_MAP_LOGIN_FAILED");
define("PHP_HOT_MAP_LOGOUT_SUCCESSFUL", "PHP_HOT_MAP_LOGOUT_SUCCESSFUL");
define("PHP_HOT_MAP_METHOD_EXECUTED", "PHP_HOT_MAP_METHOD_EXECUTED");
define("PHP_HOT_MAP_METHOD_NOT_EXISTS", "PHP_HOT_MAP_METHOD_NOT_EXISTS");
define("PHP_HOT_MAP_MAPPING_SUCCESSFUL", "PHP_HOT_MAP_MAPPING_SUCCESSFUL");
define("PHP_HOT_MAP_INTERFACE_SHOW_SUCCESSFUL", "PHP_HOT_MAP_INTERFACE_SHOW_SUCCESSFUL");
define("PHP_HOT_MAP_METHOD_STOPPED", "PHP_HOT_MAP_METHOD_STOPPED");

/**
 * phpHotMap
 *
 * This is a static class and contain some methods
 * using by phpHotMapServer
 *
 * @author Rafael Rodriguez Ramirez
 * @version 1.0
 */

class phpHotMap{

	/**
	 * Begin a session on server
	 *
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 */
	static function login($username, $password){
		if (function_exists("phpHotMap_auth")){
			$r = phpHotMap_auth($username, $password);
			if ($r === true){
				$_SESSION['phpHotMap_user'] = $username;
				return true;
			}
		}
		return false;
	}

	/**
	 * Close session
	 */
	static function logout(){
		unset($_SESSION['phpHotMap_user']);
		return PHP_HOT_MAP_LOGOUT_SUCCESSFUL;
	}

	/**
	 * Verify authentication
	 * @return boolean
	 */
	static function verifyAuth(){
		return isset($_SESSION['phpHotMap_user']);
	}

	/**
	 * Check if current user can access to specific method
	 *
	 * @param string $method
	 * @return boolean
	 */
	static function checkMethodAccess($method){
		if (function_exists("phpHotMap_chkma")){
			$v = self::verifyAuth();
			if ($v === true){
				$r = phpHotMap_chkma($_SESSION['phpHotMap_user'], $method);
				return $r;
			}
		}
		return false;
	}

	/**
	 * Return the client IP address
	 *
	 * @return string
	 */
	static function getClientIPAddress() {
		if (!isset($_SERVER)){
			$_SERVER = $HTTP_SERVER_VARS;
		}
		$ip = '127.0.01';
		if (isset($_SERVER['REMOTE_ADDR'])){
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if (isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])){
			$ip = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
		}
		return $ip;
	}

	/**
	 * Check IP address in IP range
	 *
	 * @param string $from
	 * @param string $to
	 * @param string $ip
	 * @return boolean
	 */
	static function checkRangeIP($from, $to, $ip){
		$from = ip2long($from);
		$to = ip2long($to);
		$ip = ip2long($ip);
		return $ip>=$from && $ip<=to;
	}

	static function isArrayOfArray($arr){
		$is = false;
		if (is_array($arr)){
			$is = true;
			foreach ($arr as $v){
				if (!is_array($v)){
					$is = false;
					break;
				}
			}
		}
		return $is;
	}

	static function isArrayOfObjects($arr){
		$is = false;
		if (is_array($arr)){
			$is = true;
			foreach ($arr as $v){
				if (!is_object($v)){
					$is = false;
					break;
				}
			}
		}
		return $is;
	}

	static function isNumericList($arr){
		$is = false;
		if (is_array($arr)){
			$is = true;
			foreach ($arr as $v){
				if (!is_numeric($v)){
					$is = false;
					break;
				}
			}
		}
		return $is;
	}
	
	static function getHTMLOf($mixed){
		$html = "";
		if (is_array($mixed)){
			if (self::isArrayOfArray($mixed) === true){
				$html = "<table>";

				// header
				foreach ($mixed as $key_row => $row){
					$html .= "<tr>";
					foreach ($row as $key_col => $col){
						$html .= "<th>$key_col</th>";
					}
					$html .= "</tr>";
					break;
				}

				// rows
				foreach ($mixed as $key_row => $row){
					$html .= "<tr>";
					foreach ($row as $key_col => $col){
						$html .= "<td>".self::getHTMLOf($col)."</td>";
					}
					$html .= "</tr>";
				}
				$html .= "</table>";
			} elseif (self::isArrayOfObjects($mixed)){
					
				$html = "<table>";

				// header
				foreach ($mixed as $key_row => $row){
					$html .= "<tr>";
					$vars = get_object_vars($row);
					
					foreach ($vars as $key_col => $col){
						$html .= "<th>$key_col</th>";
					}
					$html .= "</tr>";
					break;
				}

				// rows
				foreach ($mixed as $key_row => $row){
					$vars = get_object_vars($row);
					$html .= "<tr>";
					foreach ($vars as $key_col => $col){
						$html .= "<td>".self::getHTMLOf($col)."</td>";
					}
					$html .= "</tr>";
				}
				$html .= "</table>";
				
			} elseif (self::isNumericList($mixed)) {
				$html = "<table class \"numeric-list\">";
				foreach ($mixed as $key => $v){
					$html .= "<td>$v</td>";
				}
				$html .= "</table>";
			} else {
				$html = "<ul class = \"array\">";
				foreach ($mixed as $key => $value){
					$t = "";
					if (!is_numeric($key) && trim("$key") != "" && $key != null)
						$t = "$key: <br>";
					$html .= "<li> ".self::getHTMLOf($value)."</li>";
				}
				$html .= "</ul>";
			}
		} else {
			if (is_object($mixed)){
				$html = get_class($mixed).": <table>";
				$vars = get_object_vars($mixed);

				foreach ($vars as $var){
					$html .= "<li>".self::getHTMLOfArray($mixed->$var)."</li>";
				}
				$html .= "</ul>";
			} else {
				if (is_bool($mixed))
				$html = ($mixed === true? "TRUE": "FALSE");
				else{
					$html = "<label>$mixed</label>";
				}
			}
		}

		return $html;

	}
}

/**
 * Server instance
 *
 * @author Rafael Rodriguez Ramirez
 * @version 1.0
 */

/*
 How to use?

 $server = new phpHotMapServer();  // Server instance
 $server->addMethod("getEnterprise", "reu");  // Add public method
 $server->addMethod("Company::getEmployees", true); // Add private method
 $server->addData("Company", array("name" => "My Company", "phone" => "(444)-485758")); // Add some data
 $server->go();
 */

class phpHotMapServer {

	private $server_name = "";
	private $methods = array();
	private $data = array();

	/**
	 * Constructor
	 *
	 * @param string $server_name
	 * @return phpHotMapServer
	 */
	public function phpHotMapServer($server_name = "phpHotMap Server"){
		$this->server_name = $server_name;
	}
	/**
	 * Add data
	 *
	 * @param string $var
	 * @param mixed $value
	 */
	public function addData($var, $value){
		$this->data[$var] = $value;
	}

	/**
	 * Register method
	 *
	 * @param string $name
	 * @param mixed $params
	 * @param boolean $params_complex
	 * @param boolean $security
	 * @param array $hosts
	 */
	public function addMethod($name, $params = array(), $params_complex = false, $security = false, $hosts = array(), $description = "") {
		$params = !is_array($params) ? explode(",", $params): $params;

		foreach ($params as $key => $param){
			$params[$key] = trim($param);
		}

		$this->methods[$name] = array(
			"security" => $security,
			"hosts" => $hosts,
            "name" => $name,
            "params" => $params,
			"params_complex" => $params_complex,
			"description" => $description
		);
	}

	/**
	 * Publish methods: send a json to browser
	 *
	 */
	private function publish() {

		echo "{";
		$j = 0;
		$clases = array();
		foreach ($this->methods as $key => $m) {
			if (strpos($key, "::") !== false) {
				$arr = explode("::", $key);

				if (!isset($clases[$arr[0]])){
					$clases[$arr[0]] = $arr[0] . ": {";
				}
				else{
					$clases[$arr[0]] .= ",";
				}

				if ($m['params_complex'] == true) {
					$clases[$arr[0]] .= "{$arr[1]}: function(params){";
				} else {
					$clases[$arr[0]] .= "{$arr[1]}: function(";

					$i = 0;
					foreach ($m['params'] as $p) {
						if ($i++ > 0)
						$clases[$arr[0]] .= ",";
						$clases[$arr[0]] .= "$p";
					}

					$clases[$arr[0]] .= "){var params = {};";
					foreach ($m['params'] as $p) {
						$clases[$arr[0]] .= "params.$p = $p;";
					}
				}
				$clases[$arr[0]] .= "return phpHotMap.call(this.__server, '{$arr[0]}::{$arr[1]}',params);}";
			} else {
				echo $j++ > 0? ", ":"";

				if ($m['params_complex'] == true) {
					echo "$key: function(params){";
				} else {
					echo "$key: function(";

					$i = 0;
					foreach ($m['params'] as $p) {
						echo $i++ > 0? ",":"";
						echo "$p";
					}

					echo ($i>0?", ":"")."async){var params = {};";
					foreach ($m['params'] as $p) {
						echo "params.$p = $p;";
					}
				}
				echo "return phpHotMap.call(this.__server, '$key',params);}";
			}
		}

		$i = 0;
		$data = $this->data;
		foreach ($clases as $key => $c){
			echo $i == 0 && $j > 0?",":"";
			echo $i > 0?",":"";
			echo "$c";
			if (isset($data[$key])){
				$js = json_encode($data[$key]);
				if (substr($js,0,1)=="{"){
					$js = substr($js,1,strlen($js)-2);
				}
				echo ",$js}";
				unset($data[$key]);
			} else
			echo "}";
			$i++;
		}

		$k = 0;
		foreach ($data as $var => $value){
			echo $k == 0 && $i>0?",":"";
			echo $k > 0?",":"";
			$this->cleanVarName($var);
			echo "\"$var\"".": " .json_encode($value);
			$k++;
		}

		echo "}";
	}

	/**
	 * Clean a variable name
	 *
	 * @param string $var
	 * @return string
	 */
	private function cleanVarName(&$var){
		$valid = "abcdefghijklmnopqrstuvwxyz1234567890_$";
		for($i=0; $i < strlen($var); $i++){
			if (strpos(strtolower($valid), strtolower($var[$i])) === false){
				$var = str_replace($var[$i], "_", $var);	
			}
		}
		return $var;
	}
	
	/**
	 * Execute a method
	 *
	 * @param string $method
	 */
	private function execute($method){

		if (!isset($this->methods[$method]))
		return PHP_HOT_MAP_METHOD_NOT_EXISTS;
			
		// Execute hook before
		if (function_exists("phpHotMap_before")){
			$r = phpHotMap_before($method);
			if ($r == false)
				return PHP_HOT_MAP_METHOD_STOPPED;
		}

		// Execute method
		$result = null;
		if (isset($this->methods[$method])) {
			$method = $this->methods[$method];
			$instruction = "{$method['name']}(";
			$i = 0;
			foreach ($method['params'] as $p) {
				$instruction .= $i++ > 0?", ":"";
				if (isset($_POST[$p])){
					$instruction .= '$_POST["' . $p . '"]';
				}
				else{
					$instruction .= "null";
				}
			}
			$instruction .= ");";
			eval('$result = ' . $instruction);
		}

		// Execute hook after
		if (function_exists("phpHotMap_after")){
			phpHotMap_after($method);
		}
		echo json_encode($result);
	}

	/**
	 * Login, logout, publish methods or execute a method
	 *
	 * @return boolean
	 */
	public function go() {

		// Client need login?
		if (isset($_GET['login'])){
			if (isset($_GET['password'])){
				$r = phpHotMap::login($_GET['login'],$_GET['password']);
				$r = $r === true? PHP_HOT_MAP_LOGIN_SUCCESSFUL: PHP_HOT_MAP_LOGIN_FAILED;
				echo $r;
				return $r;
			}
		}

		// Client need logout?
		if (isset($_GET['logout'])){
			$r = phpHotMap::logout();
			echo $r;
			return $r;
		}

		// Client need execute a specific method?
		if (isset($_GET['execute'])){
			$method = $_GET['execute'];

			if (!isset($this->methods[$method])){
				return PHP_HOT_MAP_METHOD_NOT_EXISTS;
			}

			// Check host
			$ip = phpHotMap::getClientIPAddress();
			$hosts = $this->methods[$method]['hosts'];

			foreach($hosts as $host){
				$from = $host['from'];
				$to = $hots['to'];
				$v = phpHotMap::checkRangeIP($from,$to,$ip);
				if ($v === false){
					echo "PHP_HOT_MAP_ACCESS_DENIED_HOST";
					return PHP_HOT_MAP_ACCESS_DENIED_HOST;
				}
			}

			$r = phpHotMap::checkMethodAccess($method);

			/*
			 a     b     !a || b
			 -------------------
			 t     t        t
			 t     f        f
			 f     f        t
			 f     t        t
			 */

			if (!$this->methods[$method]['security'] || $r){
				$this->execute($method);
				return PHP_HOT_MAP_METHOD_EXECUTED;
			}
			echo "PHP_HOT_MAP_ACCESS_DENIED_USER";
			return PHP_HOT_MAP_ACCESS_DENIED_USER;
		}

		// Client need the mapping?

		if (isset($_GET['mapping'])){
			$this->publish();
			return 	PHP_HOT_MAP_MAPPING_SUCCESSFUL;
		}

		// Then show interface
		$this->showFace();
		return PHP_HOT_MAP_INTERFACE_SHOW_SUCCESSFUL;
	}


	/**
	 * Show an interface with publication
	 *
	 */
	public function showFace(){
		echo "<html>\n";
		echo "<head>\n";
		echo "<style>";
		echo " body {margin: 0px; background: white;}";
		echo " h1 {padding: 5px; background: navy; color: white;}";
		echo " h2 {padding: 5px; background:  #8ad7ff; color: black;}";
		echo " h3 {padding: 5px; color: navy;}";
		echo " table th {padding: 3px; background: black; color: white;}";
		echo " table {border: 1px solid black; margin: 10px;}";
		echo " table td {border: 1px solid black;}";
		echo " table tr:hover {background: #aae2ff;}";
		echo " li {padding: 3px; margin: 0px;}";
		echo " ul.classes {padding-left: 15px; margin: 5px; list-style: square;}";
		echo " ul.array {padding-left: 15px; margin: 5px; list-style: disc;}";
		echo " ul.methods {padding-left: 15px; margin: 5px; list-style: circle;}";
		echo " ul.functions {padding-left: 15px; margin: 5px; list-style: circle;}";
		echo " label {padding-left: 5px; padding-right: 5px;}";
		echo "</style>";
		echo "</head>\n";
		echo "<body>\n";
		echo "<h1>{$this->server_name}</h1>";

		echo "<h2>Data</h2>";

		foreach ($this->data as $var=>$val){
			echo "<h3> $var</h3>";
			echo phpHotMap::getHTMLOf($val);
		}

		echo "<h2>Functions</h2>";

		echo "<ul class = \"functions\">";

		foreach ($this->methods as $function){
			if (strpos($function['name'], "::")===false){
				echo "<li> <b>function</b> {$function['name']} (";
				$i = 0;
				foreach ($function['params'] as $p){
					if ($i++ > 0){
						echo ",";
					}
					echo '$'.$p;
				}
				echo  ")".($function['description']!=""?": ".$function['description']:"")."</li>";
			}
		}

		echo "</ul>";

		echo "<h2>Methods</h2>";

		$methods = array();
		foreach ($this->methods as $function){
			if (strpos($function['name'], "::") !== false){
				$arr = explode("::", $function['name']);
				$classname = $arr[0];
				if (!isset($methods[$classname])){
					$methods[$classname] = array();
				}
				$function['name'] = $arr[1];
				$methods[$classname][$function['name']] = $function;
			}
		}
		echo "<ul class = \"classes\">";
		foreach($methods as $class => $ms){
			echo "<li>{$class}";
			echo "<ul class = \"methods\">";
			foreach ($ms as $fname => $m){
				echo "<li> <b>method</b> $fname (";
				$i = 0;
				foreach ($m['params'] as $p){
					if ($i++ > 0){
						echo ",";
					}
					echo '$'.$p;
				}
				echo  ")".($m['description']!=""?": ".$m['description']:"")."</li>";
			}
			echo "</ul>";
			echo "</li>";

		}
		echo "</ul>";
		echo "</body>\n";
		echo "</html>\n";
	}

}
?>
