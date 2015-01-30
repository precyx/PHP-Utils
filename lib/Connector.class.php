<?php
class Connector {
	/*
		Connector.class.php - Version 1.21
		mysqli + OOP
	*/
	/* statics */
	static $VERSION = "1.21";
	static $p_class = "php_out_paragraph";
	static $p_class2 = "php_out_header";
	static $p_class3 = "php_out_header_error";
	static $p_class4 = "php_out_header_success"; 
	/* privates */
	private $connection;
	private $db_name;
	
	function __construct($host, $user, $pw){
		$this->connection = new mysqli($host, $user, $pw);
		// should be $mysqli->connect_error (but not supported in PHP 5.2.9 and 5.3.0)
		if(mysqli_connect_error()) die( "<br><p class='". self::$p_class3 ."'>" ."Connection failed." ."</p>". "<br>");
		echo "<p class='". self::$p_class4 ."'> Connected successfully with" . " " . "<b>". "'".$this->connection->host_info."'". "</b>" . " </p>" ;
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
	public function info(){
		echo "<p class='". self::$p_class2 ."'>" ." Connector - Version" .self::$VERSION. " (OOP style)". "</p>";
	}
	
	public function selectDB($db){
		$this->connection->select_db($db) or die("<br>" ."<p class='". self::$p_class3 ."'>". "Failed to select database: ". "'".$db ."'"."<br>". $this->connection->error ."</p>" );
		echo "<br>". "<p class='". self::$p_class4 ."'>" . "Database successfully selected: " . "<b>". "'".$db."'". "</b>". "</p>". "<br>";
		$this->db_name = $db;
	}

	public function listAllDB(){
		$result = $this->connection->query("SHOW DATABASES");
		echo "<br>". "<p class='". self::$p_class2 ."'>" ."Found ". "databases" . ":". "</p>";
		echo "<p class='". self::$p_class ."'>";
		while ($row = $result->fetch_array(MYSQLI_BOTH)) {
			echo "'".$row[0]."'"."<br>";
		}
		echo "</p>";
	}

	public function listAllTables(){
		if(!$this->db_name) die("<p class=". self::$p_class3 .">"." "."Could not list tables: No database selected " ."<p>");
		$result = $this->connection->query("SHOW TABLES FROM $this->db_name");
		echo "<p class='". self::$p_class2 ."'>" ." Tables in database: "."'".$this->db_name."'". "</p>";
		echo "<p class='". self::$p_class ."'>";
		while ($row = $result->fetch_array(MYSQL_BOTH)) {
			echo $row[0]."<br>";
		}
		echo "</p>";

	}

	public function createDB($db_name){
		$result = $this->connection->query("CREATE DATABASE $db_name");
		if(!$result){
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

	/*
		queryMysql - schickt eine MYSQL Abfrage an den Server.
		@param string $sql - Der MYSQL String.
	*/
	public function queryMysql($sql){
		$result = $this->connection->query($sql);
		//error
		if($result == false) echo "<p class=". self::$p_class3 .">" . "Failed mysql query: " . "<b>". $sql."</b> <br>"." ".$this->connection->error."</p><br>";
		//create,drop,delete,insert,update
		if($result == true) echo "<p class=". self::$p_class4 .">" . "Performed mysql query: " . "<b>". $sql."</b>" ."</p>";
		//select
		if(is_object($result)) {
			echo "<p class=". self::$p_class4 .">" . "List entries here: "."</p>";
			//
			// table
			echo "<table border='1'>";
			// table header
			echo "<tr>";
			$rows = $result->fetch_fields();
			$i = 0;
			while($i < count($rows) ){
				echo "<td><b>".$rows[$i]->name."</b></td>";
				$i++;
			}
			echo "</tr>";
			// table body
			$j = 0;
			while($row = $result->fetch_array(MYSQLI_BOTH)){
				echo "<tr>";
				while($j < $this->connection->field_count){
					echo "<td>".$row[$j]."</td>";
					$j++;
				}
				echo "</tr>";
				$j = 0;
			}
			echo "</table><br>";
		}
	}//end-func


	/*
	public function numRowsFromTable($table){
		$query = mysql_query("SELECT * FROM $table", $this->connection);
		echo "<p class=". self::$p_class4 .">"."Rows in "."'".$table."'". ": ". "<b>". mysql_num_rows($query). "</b>"."</p>". "<br>";
	}

	public function numFieldsFromTable($table){
		$query = mysql_query("SELECT * FROM $table", $this->connection);
		echo "<p class=". self::$p_class4 .">"."Fields in "."'".$table."'". ": "."<b>". mysql_num_fields($query)."</b>"."</p>". "<br>";
	}
	*/

}
?>