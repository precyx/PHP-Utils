<?php
class Connector {

/*
	Connector.class.php - Version 1.08

	mysql -> deprecated
	
*/

private $host;
private $user;
private $pw;
private $connection;
private $db;

static $p_class = "php_out_paragraph";
static $p_class2 = "php_out_header";
static $p_class3 = "php_out_header_error";
static $p_class4 = "php_out_header_success"; 


	function __construct($host, $user, $pw){
		$this->host = $host;
		$this->user = $user;
		$this->pw = $pw;
		$this->connect();
	}
	function __toString(){
		
		$string = 
		"<br>".
		"String description from class: ". "'".get_class($this) . "'" . "<br>".
		"Host: " . $this->host . "<br>".
		"User: " . $this->user . "<br>".
		"Pass: " . $this->pw . "<br>".
		"<br>";
		
		return $string;
	}
	function __destruct(){
		//echo "Object was deleted successfully.<br>";
	}
	
	private function connect(){
		$connection = mysql_connect($this->host, $this->user, $this->pw) or die( "<br><p class='". self::$p_class3 ."'>" ."Connection failed." ."</p>". "<br>");
		if($connection) echo "<p class='". self::$p_class4 ."'> Connected successfully with" . " " . "<b>". "'".$this->host."'". "</b>" . " </p>" ;
		$this->connection = $connection;
	}
	
	public function selectDB($db){
		mysql_select_db($db) or die("<br>" ."<p class='". self::$p_class3 ."'>". "Failed to select database: ". "'".$db ."'"."<br>". mysql_error() ."</p>" );
		echo "<br>". "<p class='". self::$p_class4 ."'>" . "Database successfully selected: " . "<b>". "'".$db."'". "</b>". "</p>". "<br>";
		$this->db = $db;
	}

	public function listAllTables(){
		$query = mysql_query("SHOW TABLES FROM $this->db" );
		echo "<p class='". self::$p_class2 ."'>" ." Tables in database: "."'".$this->db."'". "</p>";
		echo "<p class='". self::$p_class ."'>";
		while ($row = mysql_fetch_array($query)) {
			echo $row[0]."<br>";
		}
		echo "</p>";

	}

	public function listAllDB(){
		$query = mysql_query("SHOW DATABASES");
		//echo count($entry_array);
		echo "<br>". "<p class='". self::$p_class2 ."'>" ."Found ". "databases" . ":". "</p>";
		$db_array = [];
		echo "<p class='". self::$p_class ."'>";
		while ($row = mysql_fetch_array($query)) {
			echo "'".$row[0]."'"."<br>";
			array_push($db_array, $row[0]);
		}
		echo "</p>";
		return $db_array;
	}


	public function createDB($db_name){
		$query = mysql_query("CREATE DATABASE $db_name",$this->connection);
		if(!$query){
			echo "<p class=". self::$p_class3 .">"." "."Database already exists: "."'".$db_name."'"."<p>";
		}
		else{
			echo "<p class=". self::$p_class4 .">"."Database"." successfully created: "."'".$db_name."'"."<p>";
		}
	}


	public function listEntriesFromTable($table, $limit = 20){
		$sql = "SELECT * FROM $table LIMIT 0, $limit";
		$this->queryMysql($sql);
	}

	public function numRowsFromTable($table){
		$query = mysql_query("SELECT * FROM $table", $this->connection);
		echo "<p class=". self::$p_class4 .">"."Rows in "."'".$table."'". ": ". "<b>". mysql_num_rows($query). "</b>"."</p>". "<br>";
	}

	public function numFieldsFromTable($table){
		$query = mysql_query("SELECT * FROM $table", $this->connection);
		echo "<p class=". self::$p_class4 .">"."Fields in "."'".$table."'". ": "."<b>". mysql_num_fields($query)."</b>"."</p>". "<br>";
	}

	/*
		queryMysql - schickt eine MYSQL Abfrage an den Server.
		@param string $sql - Der MYSQL String.
	*/
	public function queryMysql($sql){
		$query = mysql_query($sql);
		//error
		if($query == false) echo "<p class=". self::$p_class3 .">" . "Failed mysql query: " . "<b>". $sql."</b> <br>"." ".mysql_error()."</p><br>";
		//create,drop,delete,insert,update
		if($query == true) echo "<p class=". self::$p_class4 .">" . "Performed mysql query: " . "<b>". $sql."</b>" ."</p>";
		//select
		if(is_resource($query)) {
			$num_fields = mysql_num_fields($query);
			$num_rows = mysql_num_rows($query);
			$i = 0;
			echo "<p class=". self::$p_class4 .">" . "List entries here: "."</p>";
			//
			// table
			echo "<table border='1'>";
			// table header
			echo "<tr>";
			while($i < $num_fields){
				echo "<td><b>".mysql_field_name($query, $i)."</b></td>";
				$i++;
			}
			echo "</tr>";
			$j = 0;
			// table body
			while($row = mysql_fetch_array($query)){
				echo "<tr>";
				while($j < $num_fields){
					echo "<td>".$row[$j]."</td>";
					$j++;
				}
				echo "</tr>";
				$j = 0;
			}
			echo "</table><br>";
		}
	}//end-func


}
?>