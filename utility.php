<?php
	function displayError(){
		global $config;
		global $mydb;
		
		$errors=$config["error_messages"];
		if(isset($_GET["error"])){
			if(array_key_exists($mydb->sanitize($_GET["error"]),$errors))
				echo $errors[$mydb->sanitize($_GET["error"])];
			else echo "Ismeretlen hiba történt: ".$mydb->sanitize($_GET["error"]);
		}
	}
	
	//$config a kiolvasott beállításokat tartalmazó tömb neve (már az initial.php-ben létrejön)
	function displayConfig(array $config){
		if(!isset($config)){
			echo "Nem látszik a \$config (iniből)!";
			return;
		}
		
		foreach($config as $section => $arr){
			echo "[".$section."]<br />";
			if(is_array($arr)){
				foreach($arr as $key => $value){
					if(is_array($value)){
						foreach($value as $k =>$v){
							echo "{$key}[$k]=";
							if(is_numeric($v)) echo $v;
							else echo "\"$v\"";
							echo "<br />";
						}
					}
					else{
						echo "$key=";
						if(!is_numeric($value)) echo "\"$value\"";
						else echo $value;
						echo "<br />";
					}
				}
			}
			echo "<br />";
		}
	}
	
	//kimenti a tömbben ideiglenesen tárolt beállításokat a konfig fájlba
	//pl. saveConfig("config.ini", $config);
	function saveConfig($file, array $options){
		$tmp = '';
		foreach($options as $section => $values){
			$tmp .= "[$section]\r\n";
			foreach($values as $key => $val){
				if(is_array($val)){
					foreach($val as $k =>$v){
						$tmp .= "{$key}[$k]=";
						if(is_numeric($v)) $tmp.=$v;
						else $tmp.="\"$v\"";
						$tmp.="\r\n";
					}
				}
				else{
					$tmp .= "$key=";
					if(!is_numeric($val)) $tmp.="\"$val\"";
					else $tmp.=$val;
					$tmp.="\r\n";
				}
			}
			$tmp .= "\r\n";
		}
		file_put_contents($file, $tmp);
		unset($tmp);
	}
	
	//----------------- használaton kívüli függvények --------------------
	
	/*
	function setImgDownload($imagePath) {           
		$image = imagecreatefromjpeg($imagePath);
		header('Content-Type: image/jpeg');
		imagejpeg($image);
	}
	
	*/
?>