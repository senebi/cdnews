<?php
class category{
	private $id;
	private $name;
	private $imgUrl;
	
	//külső osztályok config fájl
	private $db;
	private $user;
	private $dbConfig;
	private $imgConfig;
	private $errors;
	
	public function __construct($conf){
		global $mydb;
		global $user;
		
		$this->user=$user;
		$this->db=$mydb;
		$this->dbConfig=$conf["database_connection"];
		$this->imgConfig=$conf["images"];
		$this->errors=$conf["error_messages"];
		$this->db->checkIfExists();
		
		//ha az add_entry_formról jövünk
		if(isset($_POST["btn_add_category"])){
			//mentetlen (új) kategóriának 0 az id-je, vagyis nem létezik, ezt később vizsgálhatjuk
			$this->id=0;
			$this->name=$this->db->sanitize($_POST["uj_kateg"]);
			//esetleg $this->imgUrl=$this->db->sanitize(/*kategória kép föltöltés után az url*/);
		}
	}
	
	//adatbázisba mentés
	public function add(){
		$this->db->checkIfExists();
		
		$insertSql="insert into ".$this->dbConfig["table_prefix"]."kategoriak (megnevezes, kepurl) values('".$this->name."','".$this->imgUrl."')";
		
		//kategória hozzáadása
		$this->db->runQuery($insertSql);
		
		echo "A kategória sikeresen hozzáadva.<br />";
		echo "Mit szeretnénk tenni? <a href='add_category_form.php'>Új kategória létrehozása</a> vagy <a href='list_entries.php'>Bejegyzések megtekintése</a>";
	}
	
	//adatbázisból kiolvasás
	public function read($entryId=null, $outputHTML="select"){
		$output="";
		$prefix=$this->dbConfig["table_prefix"];
	
		//minden kategória kiolvasása
		$selectSql="select * from ".$prefix."kategoriak order by id";
		$joinSql="select b.*, k.* from ".$prefix."bejegyzesek as b
			inner join ".$prefix."kategoriak as k on ".
			"b.kateg_id=k.id
			where b.id=".$entryId;
		
		$this->db->runQuery($selectSql);
		$res=$this->db->getLastResult();
		if($res){
			if($res->num_rows>0){
				if($outputHTML!="select")
					$output="<ul>";
				
				if($id!=null){
					$this->db->runQuery($joinSql);
					$joinRes=$this->db->getLastResult();
					$selectedCategoryId=0;
					if($joinRes){
						if($joinRes->num_rows>0){
							if($joinRow=$joinRes->fetch_assoc())
								$selectedCategoryId=$joinRow["k.id"];
						}
					}
				}
				while($row=$res->fetch_assoc()){
					if($outputHTML=="select"){
						$output.="<option value=".$row["id"];
						if($id!=null && $selectedCategoryId==$row["id"]) $output.=" selected";
						$output.=">".$row["megnevezes"]."</option>";
					}
					else $output.="<li>".$row["megnevezes"]."</li>";
				}
				if($outputHTML!="select")
					$output.="</ul>";
				return $output;
			}
			else $output.="<p>Jelenleg nincs kategória.</p>";
		}
		else $output.="Hiba történt lekérdezés közben! Próbáld újra!";
		
		unset($prefix);
		return $output;
	}
	
	//adatbázisban szerkesztés
	public function update(){
	}
	
	//adatbázisból törlés
	public function delete(){
	}
	
	//debug célból a memóriában tárolt adatok kiíratása
	public function outputData(){
		echo "id=".$this->id."<br />";
		echo "name=".$this->name."<br />";
		echo "imgUrl=".$this->imgUrl."<br />";
	}
};
?>