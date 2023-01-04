<?php
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."HTMLPurifier.auto.php");

class entry{
	private $id;
	private $slug;
	private $title;
	private $publishDate;
	private $content;
	private $author;
	private $categId;
	private $category;
	
	//konfig fájlból jövő értékek
	private $dbConfig;
	private $imgConfig;
	private $errors;
	
	//függőségek: amikhez más osztályok meghívása szükséges (entry.class.php előtt)
	private $purifier;
	private $db;
	private $user;
	
	public function __construct($id=null, $conf){
		global $mydb;
		global $user;
		
		$this->db=$mydb;
		$this->user=$user;
		$this->dbConfig=$conf["database_connection"];
		$this->imgConfig=$conf["images"];
		$this->errors=$conf["error_messages"];
		
		$this->db->checkIfExists();
		
		if(!isset($this->purifier)) $this->purifier = new HTMLPurifier();
		
		//ha az add_entry_formról jövünk
		if(isset($_POST["btn_add_entry"])){
			$this->title=$this->db->sanitize($_POST["uj_cim"]);
			$this->content=$this->purifier->purify($_POST["uj_tartalom"]);
			$this->author=$this->user->getUserName();
			$this->publishDate=date("Y-m-d");
			$this->generateSlug("_");
			$this->categId=$this->db->sanitize($_POST["kateg"]);
		}
		
		if($id!=null){
			$this->load($id);
			$this->category=$this->getCategory();
		}
	}
	
	public function isEntryLoaded(){
		return isset($this->id);
	}
	
	//betöltés adatbázisból
	public function load($id=null){
		if(!is_numeric($id)) $id="'".$id."'";
		
		$selectSql="select b.*, megnevezes from ".$this->dbConfig["table_prefix"]."bejegyzesek as b
		inner join ".$this->dbConfig["table_prefix"]."kategoriak as k on ".
		"b.kateg_id=k.id
		where b.id=".$id;
		
		$this->db->runQuery($selectSql);
		$res=$this->db->getLastResult();
		if($res){
			if($res->num_rows>0){
				$data=$res->fetch_assoc(); //vizsgálat nélkül csak akkor, ha biztosak vagyunk benne, hogy 1 sor van!
				//ha nem biztos, hogy van-e sor és mennyi, akkor while($row=$res->fetch_assoc()){} kell!
				$this->id=$data["id"];
				$this->title=$data["cim"];
				$this->content=htmlspecialchars_decode($data["tartalom"]);
				$this->author=$data["user_fk"];
				$this->publishDate=$data["datum"];
				$this->slug=$data["slug"];
				$this->categId=$data["kateg_id"];
			}
		}
	}
	
	//adatbázisba mentés
	public function add(){
		$this->db->checkIfExists();
		
		if($this->content=="" || $this->title==""){
			echo "<p>Mindkét mező kitöltése kötelező!</p>";
			echo "<p><a href='add_entry_form.php'><-- vissza</a></p>";
			return;
		}
		
		try{
			$exists=$this->slugExists($this->slug);
			if($exists){
				echo "<p>Ez az egyszerűsített cím már foglalt! Válassz másikat!</p>";
				echo "<p><a href='add_entry_form.php'><-- vissza</a></p>";
				return;
			}
		}
		
		catch(Exception $e){
			echo "Hiba: ".$e->getMessage();
			echo "<p><a href='add_entry_form.php'><-- vissza</a></p>";
			return;
		}
		
		//bejegyzés hozzáadása
		$insertSql="insert into ".$this->dbConfig["table_prefix"]."bejegyzesek (user_fk,datum,cim,slug,tartalom,kateg_id) values('".
		$this->author."','".$this->publishDate."','".$this->title."','".$this->slug."','".htmlspecialchars($this->content)."',".$this->categId.")";
		
		$this->db->runQuery($insertSql);
		$res=$this->db->getLastResult();
		
		if($res)
			echo "A bejegyzés sikeresen hozzáadva.";
		else{
			echo "Hiba történt! Próbáld újra!";
		}
		echo "<br />Mit szeretnénk tenni? <a href='add_entry_form.php'>Új bejegyzés létrehozása</a> vagy <a href='list_entries.php'>Bejegyzések megtekintése</a>";
	}
	
	public function slugExists($testSlug){
		$this->db->checkIfExists();
		
		$selectSql="select slug from ".$this->dbConfig["table_prefix"]."bejegyzesek where slug='".$testSlug."'";
		$this->db->runQuery($selectSql);
		$res=$this->db->getLastResult();
		if($res){
			if($res->num_rows>0)
				return true;
			else return false;
		}
		else throw new Exception("Hiba történt ellenőrzés közben!\nA bejegyzés nem jött létre!");
	}
	
	private function truncate_chars($string,$length=100,$append="..."){
		$string = trim($string);

		if(mb_strlen($string, "UTF-8") > $length){
			$string=trim(mb_substr($string,0,$length, "UTF-8"));
			$string = wordwrap($string, $length);
			$string = explode("\n", $string); //explode(..., ..., 2) az eredeti

			//$string = $string[0] . $append;
		}
		
		return $string;
	}
	
	public function truncate_words($text, $limit, $ellipsis = '...') {
		$words = preg_split("/[\n\r\t ]+/", $text, $limit + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);
		if (count($words) > $limit) {
			end($words); //ignore last element since it contains the rest of the string
			$last_word = prev($words);
			   
			$text =  substr($text, 0, $last_word[1] + strlen($last_word[0]));
			if(mb_substr($text,-3,3,"UTF-8")!="...") $text.= $ellipsis;
		}
		return $text;
	}
	
	//kiolvassa a bejegyzés első képét, ha van; ha nincs, akkor az alapértelmezettet veszi elő
	public function getFirstImage($source="html", $input, $output="html", $classes=""){
		global $config;
		$imgConfig=$config["images"];

		(isset($_SERVER['HTTPS'])) ? $protocol="https://" : $protocol="http://";
	
		//$source lehetséges értékei
		$validSources=array("url", "html");
		$validOutputs=array("url", "html");
		if(!in_array($source, $validSources))
			return "Hibás \$source paraméter a képhez: ".$source;
		
		$dom = new DOMDocument('1.0');
		
		if($source==$validSources[0]){
			$input=$protocol.$_SERVER["SERVER_NAME"].$input;
			// Loading HTML content in $dom
			@$dom->loadHTMLFile($input);
		}
		elseif($source==$validSources[1]){
			@$dom->loadHTML($input);
		}

		// Selecting all image i.e. img tag object
		$anchors = $dom -> getElementsByTagName('img');

		if($anchors->length>0){
			$src = $anchors[0]->getAttribute('src');
			if(in_array($output, $validOutputs)){
				if($output=="url") return $src;
			}
			$alt = $anchors[0]->getAttribute('alt');
			$height = $anchors[0]->getAttribute('height');
			$width = $anchors[0]->getAttribute('width');

			$image='<img ';
			if($classes!="")
				$image.=' class="'.$classes.'"';
			$image.=' src="'.$src.'" alt="'.$alt.'"';
			if($height!="")
				$image.=' height="'. $height.'"';
			if($width!="")
				$image.=' width="'.$width.'"';
			$image.=' />';
			return $image;
		}
		
		if(file_exists($_SERVER["SERVER_NAME"].$imgConfig["upload_dir.thumb"].$imgConfig["default_cover_img.name"])){
			$src=$imgConfig["upload_dir.thumb"];
			$height="";
			$width="";
		}
		else{
			$src=$imgConfig["upload_dir.img"];
			$height=200;
			$width=200;
		}
		$src.=$imgConfig["default_cover_img.name"];
		
		if(in_array($output, $validOutputs)){
			if($output=="url") return $src;
		}
		
		$image='<img ';
		$alt="nincs kép";
		if($classes!="")
			$image.=' class="'.$classes.'"';
		$image.=' src="'.$src.'" alt="'.$alt.'"';
		if($height!="")
			$image.=' height="'. $height.'"';
		if($width!="")
			$image.=' width="'.$width.'"';
		$image.=' />';
		
		return $image;
	}

	private function CreateCard($id, $title, $text, $sizes, $classes="", $html=null) {
		//a $sizes tömbként kerül átadásra
		$output="";
		$output.='<div class="card';
		if($classes!="") $output.=' '.$classes;
		foreach($sizes as $screen => $size){
			$class=" col-";
			if(!is_numeric($screen)) $class.=$screen."-";
			//ha nincs megadva $screen szövegesen, akkor automatikusan numerikus indexet kap
			$class.=$size;
			$output.=$class;
		}
		
		$output.='">';
		if(!is_null($html)){
			$imgClass="card-img-top";
			$output.='<a href="'.$_SERVER["PHP_SELF"].'?id='.$id.'">'.$this->getFirstImage("html", $html, "html", $imgClass).'</a>';
		}
		$output.='<div class="card-body">
				<h5 class="card-title"><a href="'.$_SERVER["PHP_SELF"].'?id='.$id.'">'.$title.'</a></h5>
				<p class="card-text">'.$text.'</p>
			  </div>';
		$output.='</div>';
		return $output;
	}
	
	//adatbázisból kiolvasás
	public function readAll($maxColUnits, $columnSizes, $rowClasses=""){
		$output="";
	
		//minden bejegyzés kiolvasása
		$selectSql="select * from ".$this->dbConfig["table_prefix"]."bejegyzesek order by datum desc";
		
		$this->db->runQuery($selectSql);
		$res=$this->db->getLastResult();
		$i=1;

		if($maxColUnits>12) $maxColUnits=12;
		elseif($maxColUnits<1) $maxColUnits=1;
		$maxColUnits=round($maxColUnits);
		$maxColumns=floor($maxColUnits/$columnSizes["md"]);
		
		if($res){
			if($res->num_rows>0){
				while($row=$res->fetch_assoc()){
					$html=htmlspecialchars_decode($row["tartalom"]);
					$rawContent=strip_tags($html);
					$cardId=$row["id"];
					$cardTitle=$row["cim"];
					$cardText=$this->truncate_words($rawContent,8);
					
					if($i%$maxColumns==1){
						$output.='<div class="row';
						if($rowClasses!="") $output.=' '.$rowClasses;
						$output.='">';
					}

					$output.=$this->CreateCard($cardId, $cardTitle, $cardText, $columnSizes, "m-3", $html);

					if($i%$maxColumns==0 || $i==$res->num_rows)
						$output.='</div>';
					$i++;
				}
				return $output;
			}
			else $output.="Jelenleg nincs bejegyzés.";
		}
		else $output.="Hiba történt lekérdezés közben! Próbáld újra!";
		
		return $output;
	}
	
	public function get($subject){
		if(property_exists($this, $subject)){
			return $this->$subject;
		}
		else return "A megadott tagváltozó nem létezik!";
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getAuthor(){
		return $this->author;
	}
	
	public function getCategory(){
		if($this->isEntryLoaded()){
			$selectSql="select * from ".$this->dbConfig["table_prefix"]."kategoriak where id=".$this->categId;
				
			$this->db->runQuery($selectSql);
			$res=$this->db->getLastResult();
			if($res){
				if($res->num_rows>0){
					if($row=$res->fetch_assoc()){
						return $row["megnevezes"];
					}
				}
			}
		}
		else return false;
	}
	
	public function getPublishDate(){
		return $this->publishDate;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getContent(){
		return $this->content;
	}
	
	//adatbázisban szerkesztés
	public function update(){
		$this->db->checkIfExists();
		
		if(!isset($this->purifier)) $this->purifier = new HTMLPurifier();
		
		//ha az edit_entry_formról jövünk
		if(isset($_POST["btn_update_entry"])){
			$this->id=$this->db->sanitize($_POST["editEntryID"]);
			$this->title=$this->db->sanitize($_POST["cim"]);
			$this->content=$this->purifier->purify($_POST["tartalom"]);
			$this->generateSlug("_");
			$this->categId=$this->db->sanitize($_POST["kateg"]);
		}
		
		$updateSql="update ".$this->dbConfig["table_prefix"]."bejegyzesek set ";
		$updateSql.="cim='".$this->title."', slug='".$this->slug."', tartalom='".htmlspecialchars($this->content)."', ";
		$updateSql.="kateg_id=".$this->categId." where id=".$this->id;
		
		$this->db->runQuery($updateSql);
		$updateRes=$this->db->getLastResult();
		if($updateRes)
			echo "Sikeres módosítás.<br />";
		else echo "Hiba történt! Próbáld újra!<br />";
		echo "Mit szeretnénk tenni? <a href='add_entry_form.php'>Új bejegyzés létrehozása</a> vagy <a href='list_entries.php'>Bejegyzések megtekintése</a>";
	}
	
	//adatbázisból törlés
	public function delete(){
		$this->db->checkIfExists();
		
		$deleteSql="delete from ".$this->dbConfig["table_prefix"]."bejegyzesek where id=".$this->id;
		$this->db->runQuery($deleteSql);
		$res=$this->db->getLastResult();
		if($res) echo "<p>Sikeres törlés.</p>";
		else echo "<p>Hiba történt a bejegyzés törlése közben. <a href='delete_entry_action.php?id=".$this->id."'>Próbáld újra</a>!</p>";
		
		echo "<br />Mit szeretnénk tenni? <a href='add_entry_form.php'>Új bejegyzés létrehozása</a> vagy <a href='list_entries.php'>Bejegyzések megtekintése</a>";
	}
	
	//slug generálása a címből, alapértelmezett elválasztó a kötőjel (-)
	public function generateSlug(string $divider = '-'){
		$this->slug = preg_replace('~[^\pL\d]+~u', $divider, $this->title);
		$this->slug = iconv('utf-8', 'us-ascii//TRANSLIT', $this->slug);
		$this->slug = preg_replace('~[^-\w]+~', '', $this->slug);
		$this->slug = trim($this->slug, $divider);
		$this->slug = preg_replace('~-+~', $divider, $this->slug);
		$this->slug = strtolower($this->slug);
		if(empty($this->slug))
			$this->slug='n-a';
	}
	
	//debug célból a memóriában tárolt adatok kiíratása
	public function outputData(){
		echo "id=".$this->id."<br />";
		echo "slug=".$this->slug."<br />";
		echo "title=".$this->title."<br />";
		echo "publishDate=".$this->publishDate."<br />";
		echo "author=".$this->author."<br />";
		echo "content=".$this->content."<br />";
	}
};
?>