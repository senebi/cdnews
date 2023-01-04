<?php
class mydb{
	private $handle;
	private $result;
	
	//config fájlból
	private $dbConfig;
	private $errors;
	
	public function __construct($conf){
		$this->dbConfig=$conf["database_connection"];
		$this->errors=$conf["error_messages"];
		$this->connect();
	}
	
	public function connect(){
		if(empty($this->handle)){
			$host=$this->dbConfig["host_name"];
			$user_name=$this->dbConfig["user_name"];
			$password=$this->dbConfig["password"];
			$db_name=$this->dbConfig["db_name"];
			@$this->handle=new mysqli($host, $user_name, $password);
			if($this->handle->connect_errno){
				global $config;
				echo "Az adatbázishoz való kapcsolódás sikertelen: ".$this->handle->connect_error;
				echo "<br />Konfiguráció: <br />";
				displayConfig($config);
				exit();
			}
			$this->handle->set_charset("utf8");
		}
		else echo "Az adatbázishoz már csatlakozva van a program.";
	}
	
	public function getHandle(){
		return $this->handle;
	}
	
	public function checkIfExists(){
		return $this->handle->select_db($this->dbConfig["db_name"]);
	}
	
	public function disconnect(){
		$this->handle->close();
	}
	
	//lekérdezésekhez
	public function runQuery($sql){
		$this->result=$this->handle->query($sql) or die("Hiba: ".$this->handle->error." a köv. sorban: <b>".__LINE__."</b>");
	}
	
	public function getLastResult(){
		return $this->result;
	}
	
	//hogy csökkentsük a sikeres SQL injekciós támadások esélyét
	public function sanitize($input){
		return $this->handle->real_escape_string(stripslashes(trim($input)));
	}
};
?>