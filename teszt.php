<?php
	require("header.php");
	/*require_once("HTMLPurifier.auto.php");
	include_once("classes/entry.class.php");
	if(isset($_GET["entry_id"]))
		$entry_id=$_GET["entry_id"];
	else $entry_id=1;*/
?>

<h2>Teszt oldal</h2>
<?php
	// include composer autoload
	//require 'vendor/autoload.php';

	// import the Intervention Image Manager Class
	//use Intervention\Image\ImageManager;
	
	//FONTOS: Így nem hívjuk meg! Az új $config["images"] tömbből a ["upload_dir.img"]
	//és az ["upload_dir.thumb"] elemeket esetleg bele lehet tenni egy új $upload_dir_arr-ba
	//A szerkezetének meg kell egyeznie a régivel!
	//imageIntervention($upload_dir_arr, $imgThumbs);
	
	//crawl_page("http://localhost/cdnews/list_entries.php?id=$entry_id");
	
	//randomMappanevGen();
	$fvnev="crawl_page";
	if(function_exists($fvnev)){
		$fvnev("http://localhost/cdnews/list_entries.php?id=5");
		//call_user_func($fvnev, "http://localhost/cdnews/list_entries.php?id=5");
	}
	else echo "Nem létezik!";
	
	//-------------------------------fv. definíciók------------------------------------
	
	function crawl_page($url){
		$dom = new DOMDocument('1.0');
		  
		// Loading HTML content in $dom
		@$dom->loadHTMLFile($url);
		  
		// Selecting all image i.e. img tag object
		$anchors = $dom -> getElementsByTagName('img');
		if($anchors->length>0){
			$src = $anchors[0]->getAttribute('src');
			$alt = $anchors[0]->getAttribute('alt');
			$height = $anchors[0]->getAttribute('height');
			$width = $anchors[0]->getAttribute('width');
			
			echo '<img src="'.$src.'" alt="'.$alt.'" height="'
				. $height.'" width="'.$width.'"/>';
			return;
		}
		
		echo "sablon kép jön";
	}
	
	//kép manipulációk: létrehozás, átméretezés, átméretezett kép mentése, stb.
	function imageIntervention($upload_dir_arr, $imgThumbs){
		// create an image manager instance with favored driver
		$manager = new ImageManager(['driver' => 'gd']);
		
		$imgName="like.jpg";
		$dir=$upload_dir_arr["img"];
		$displayDir=$dir;
		if(substr($dir, 0, 1)=="/"){
			$displayDir=$_SERVER["SERVER_NAME"].$dir;
			$dir=$_SERVER['DOCUMENT_ROOT'].$dir;
		}
		// to finally create image instances
		$image = $manager->make($dir.$imgName);
		$image->resize($imgThumbs["maxwidth"], $imgThumbs["maxheight"], function($attr){
			$attr->aspectRatio();
			$attr->upsize();
		});
		
		//echo $image->response();
		
		$thumbDir=$upload_dir_arr["thumb"];
		if(substr($thumbDir, 0, 1)=="/"){
			$thumbDir=$_SERVER['DOCUMENT_ROOT'].$thumbDir;
		}
		echo '<img src="http://'.$displayDir.$imgName.'" alt="nem jó" />';
		//$image->save($thumbDir.$imgName);
	}
	
	//random mappa név generáláshoz
	function randomMappanevGen(){
		echo "PHP verzió: 7.4<br />"; //a távoli (chuushindojo.hu) domain php verzióját mutatja
		$length=mt_rand(15,20);
		echo "cdnews-".bin2hex(random_bytes($length)); //random mappanév
	}
	
	//mappa átnevezés, végig futunk a gyökérben lévő fájlokon, mappákon
	function mappaAtnevezes(){
		$rootFolder=dirname($_SERVER["PHP_SELF"]);
		if(substr($rootFolder,0,1)=="/")
			$rootFolder=substr($rootFolder,1);
			
		$directory = "..";
		if (is_dir($directory)){
			if ($open = opendir($directory)) { 
				while (($file = readdir($open)) !== false){
					echo $file;
					if($file==$rootFolder){
						echo " átnevezni!";
						rename($directory."/".$file, $file."2");
					}
					echo "<br />";
				}
			}
		}
		echo $rootFolder;
		$rootFolder=dirname($_SERVER["PHP_SELF"]);
		echo "<br />átnevezés után: ".$rootFolder;
	}
	
	//próba lekérdezés
	function lekerdezes($sql){
		$mydb->runQuery($sql);
		$res=$mydb->getLastResult();
	}
	
	//az alábbi 3 függvény innen van: https://www.pjgalbraith.com/truncating-text-html-with-php/
	function truncate_chars_remove_newlines($text, $limit, $ellipsis = '...') {
		if( mb_strlen($text, "UTF-8") > $limit ) {
			$endpos = mb_strpos(str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $text), ' ', $limit, "UTF-8");
			if($endpos !== FALSE)
				$text = trim(mb_substr($text, 0, $endpos, "UTF-8")) . $ellipsis;
		}
		return $text;
	}
	
	function truncate_chars($text, $limit, $ellipsis = '...') {
		if(mb_strlen($text, "UTF-8") > $limit ) 
			$text = trim(mb_substr($text, 0, $limit, "UTF-8"));// . $ellipsis; 
		return $text;
	}
	
	function truncate_words($text, $limit, $ellipsis = '...') {
		$words = preg_split("/[\n\r\t ]+/", $text, $limit + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);
		if (count($words) > $limit) {
			end($words); //ignore last element since it contains the rest of the string
			$last_word = prev($words);
			   
			$text =  substr($text, 0, $last_word[1] + strlen($last_word[0]));
			if(mb_substr($text,-3,3,"UTF-8")!="...") $text.= $ellipsis;
		}
		return $text;
	}
	
	include("footer.php");
?>