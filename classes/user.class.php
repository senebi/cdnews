<?php
class user{
	private $userName;
	private $loggedIn;
	private $message;
	
	//más osztály objektumok és config adatok
	private $db;
	private $dbConfig;
	private $imgConfig;
	private $errors;
	
	public function __construct($conf){
		global $mydb;
		
		$this->db=$mydb;
		$this->dbConfig=$conf["database_connection"];
		$this->imgConfig=$conf["images"];
		$this->errors=$conf["error_messages"];
		$this->db->checkIfExists();
		
		if(!isset($_SESSION["user"])){
			$_SESSION["user"]="";
			$this->userName="";
			$this->loggedIn=false;
		}
		else{
			$this->userName=$_SESSION["user"];
			if($this->userName!="")
				$this->loggedIn=true;
			else $this->loggedIn=false;
		}
		
		if(!isset($_SESSION["message"])){
			$this->setMessage("");
		}
		else $this->message=$_SESSION["message"];
		
		if(!$this->loggedIn) $this->setMessage("<p>Vendégként böngésszük az oldalt.</p>");
		else $this->setMessage("Bejelentkezve: ".$this->userName);
		
		//megnézzük, hogy érkeztek-e bejelentkezési adatok
		if(isset($_POST["login_user"],$_POST["login_pass"])){
			$loginUser=$this->db->sanitize($_POST["login_user"]);
			$loginPass=$this->db->sanitize($_POST["login_pass"]);
			$this->login($loginUser, $loginPass);
		}
	}
	
	public function isLoggedIn(){
		return $this->loggedIn;
	}
	
	public function login($loginUser, $loginPass){
		$selectSql="select * from ".$this->dbConfig["table_prefix"]."felhasznalok
		where user='".$loginUser."'";
		
		$this->db->runQuery($selectSql);
		$res=$this->db->getLastResult();
		
		//ha a bejelentkezés sikeres
		if($res->num_rows>0){
			$row=$res->fetch_assoc();
			if(password_verify($loginPass, $row['pass'])){
				$_SESSION["user"]=$loginUser;
				$this->loggedIn=true;
				$this->userName=$loginUser;
				$this->setMessage("Sikeres bejelentkezés: ".$this->userName);
			}
			else $this->setMessage("Helytelen felhasználónév vagy jelszó!");
		}
		else //ha a bejelentkezés sikertelen
			$this->setMessage("Helytelen felhasználónév vagy jelszó!");
	}
	
	public function logout(){
		$this->loggedIn=false;
		$this->userName="";
		$this->setMessage("Sikeres kijelentkezés.");
		unset($_SESSION["user"]);
		unset($_SESSION["message"]);
		session_destroy();
	}
	
	public function setMessage($msg){
		$_SESSION["message"]=$msg;
		$this->message=$_SESSION["message"];
	}
	
	public function getMessage(){
		return $this->message;
	}
	
	public function getUserName(){
		return $this->userName;
	}
	
	public function listUsers(){
		$selectSql="select user from ".$this->dbConfig["table_prefix"]."felhasznalok
		where pass is null order by user";
		
		$this->db->runQuery($selectSql);
		$res=$this->db->getLastResult();
		if($res->num_rows>0){
			$output="<ul>";
			while($row=$res->fetch_assoc()){
				$output.="<li>".$row["user"]." - <a href='addpass.php?user=".$row["user"]."'>Jelszó létrehozása</a></li>";
			}
			$output.="</ul>";
		}
		else $output="<p>Jelenleg nincs inaktív felhasználó.</p>";
		
		return $output;
	}
};
?>