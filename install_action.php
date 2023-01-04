<?php
	require("initial.php");
	
	if(!isset($_POST["btn_install"])){
		header("location: install_form.php");
		exit();
	}
	if($mydb->checkIfExists()){
		header("location: index.php");
		exit();
	}
	if($mydb->sanitize($_POST["admin_user"])==""){
		header("location: install_form.php?error=emptyUser");
		exit();
	}
	
	$config["database_connection"]["host_name"]=$mydb->sanitize($_POST["input_dbhost"]);
	$config["database_connection"]["user_name"]=$mydb->sanitize($_POST["input_dbuser"]);
	$config["database_connection"]["password"]=$mydb->sanitize($_POST["input_dbpass"]);
	$config["database_connection"]["db_name"]=$mydb->sanitize($_POST["input_dbname"]);
	$config["database_connection"]["table_prefix"]=$mydb->sanitize($_POST["table_prefix"]);
	$admin=$mydb->sanitize($_POST["admin_user"]);
?>
<h2>Telepítés</h2>
	<p>A telepítés folyamatban...</p>
<?php
	$dbConfig=$config["database_connection"];
	
	$mydb->runQuery("create database ".$dbConfig["db_name"]." character set utf8 collate utf8_hungarian_ci");
	if($mydb->checkIfExists()) echo "Az adatbázis (<i>".$dbConfig["db_name"]."</i>) létrehozva.<br />";
	else{
		echo "Az adatbázist nem sikerült létrehozni. Próbáld újra!<br />";
		echo "<a href=\"install_form.php\">Vissza</a>";
		exit();
	}
	//egész: int, tört: float, boolean: tinyint(2), rövid string: varchar(hossz), hosszú string: text
	$tbl_entries=array(
		"user_fk" => "varchar(60) not null",
		"datum" => "date not null default current_timestamp",
		"cim" => "varchar(60) not null",
		"slug" => "varchar(60) not null",
		"tartalom" => "text",
		"kateg_id" => "int not null"
	);
	$tbl_categories=array(
		"id" => "int not null auto_increment primary key",
		"megnevezes" => "varchar(60)"
	);
	
	$createSql="CREATE TABLE ".$dbConfig["table_prefix"]."felhasznalok(";
	$createSql.="user varchar(60) not null primary key, pass varchar(255))";
	
	//felhasználók tábla létrehozása
	$mydb->runQuery($createSql);
	
	echo "Felhasználók tábla létrehozva.<br />";
	
	$insertSql="insert into ".$dbConfig["table_prefix"]."felhasznalok(user) values('".$admin."')";
	//admin (első) felhasználó létrehozása
	$mydb->runQuery($insertSql);
	echo "Admin (első) felhasználó létrehozva.<br />";
	
	$createSql="create table ".$dbConfig["table_prefix"]."kategoriak(";
	$i=1;
	foreach($tbl_categories  as $fieldName => $fieldType){
		$createSql.=$fieldName." ".$fieldType;
		
		if($i<count($tbl_categories))
			$createSql.=",";
		$i++;
	}
	$createSql.=")";
	
	//kategóriák tábla létrehozása
	$mydb->runQuery($createSql);
	
	echo "Kategóriák tábla létrehozva.<br />";
	
	$categories=array("edzés", "filozófia", "beszámolók");
	foreach($categories as $name){
		$insertSql="insert into ".$dbConfig["table_prefix"]."kategoriak(megnevezes) values('".$name."')";
		//kategória létrehozása
		$mydb->runQuery($insertSql);
		echo $name." kategória létrehozva.<br />";
	}
	
	$createSql="create table ".$dbConfig["table_prefix"]."bejegyzesek(";
	$createSql.="id int not null auto_increment primary key,";
	$i=1;
	foreach($tbl_entries as $fieldName => $fieldType){
		$createSql.=$fieldName." ".$fieldType.",";
	}
	$createSql.="FOREIGN KEY (user_fk) REFERENCES ".$dbConfig["table_prefix"]."felhasznalok(user),";
	$createSql.="FOREIGN KEY (kateg_id) REFERENCES ".$dbConfig["table_prefix"]."kategoriak(id)";
	$createSql.=")";
	
	//bejegyzések tábla létrehozása
	$mydb->runQuery($createSql);
	echo "Bejegyzések tábla létrehozva.<br />";
	
	$createSql="CREATE UNIQUE INDEX egyedi_slug
	ON ".$dbConfig["table_prefix"]."bejegyzesek (slug)";
	
	//egyedi slug index létrehozása
	$mydb->runQuery($createSql);
	echo "Slug index létrehozva.<br />";
	
	$imgConf=$config["images"];
	//ha nincs original és thumb mappa a /images-ben, akkor létrehozzuk őket
	// a képekkel együtt (azokat átmásoljuk a saját helyükre a kicsit, nagyot)
	$upload_dir_arr=array($imgConf["upload_dir.img"], $imgConf["upload_dir.thumb"]);
	$defaultCoverImg=array(
		"folder" => $imgConf["default_cover_img.folder"],
		"name"   => $imgConf["default_cover_img.name"]
	);
	foreach($upload_dir_arr as $dir){
		$dir=substr($dir, 0, -1);
		$tmpArr=explode('/', $dir);
		$lastSubfolder=end($tmpArr);
		if(substr($dir, 0, 1)=="/"){
			$dir=$_SERVER['DOCUMENT_ROOT'].$dir;
		}

		$from=$defaultCoverImg["folder"].$lastSubfolder."/".$defaultCoverImg["name"];
		$to=$dir."/".$defaultCoverImg["name"];
		if(!file_exists($dir) && !is_dir($dir)){
			echo $dir." mappa létrehozása...";
			if(mkdir($dir, 0777, true)){
				echo " kész,";
				//alap képek másolása
				if(copy($from, $to))
					echo " alapértelmezett kép bemásolva.";
				else echo " alapértelmezett kép nem másolható!";
			}
			else echo " sikertelen!";
		}
		else{
			echo "A(z) ".$dir." mappa már létezik!";
			//ha már létezik a mapp, akkor is másoljuk az alap képeket
			if(!file_exists($dir."/".$defaultCoverImg["name"])){
				if(copy($from, $to))
					echo " Alapértelmezett kép bemásolva.";
				else echo " Alapértelmezett kép nem másolható!";
			}
		}
		echo "<br />";
	}

	//ha adatbázis hiba volt, akkor már kiszálltunk a program futásból
	//más hiba esetén (szól, de) simán kimenti a rossz adatokat is a configba!!!
	saveConfig("config.ini", $config);
?>
	<p>A telepítés befejeződött.<br />
<?php
	$length=mt_rand(15,20);
	echo "Generált titkos mappanév: <i>cdnews-".bin2hex(random_bytes($length))."</i>";
?>
	<br />
	Nevezd át erre a szülő mappát (amiben ez a fájl is van).
	</p>
	<h3><a href="index.php">Ugrás a kezdőlapra</a></h3>
<?php
	include("footer.php");
?>