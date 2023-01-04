<?php 
if(!file_exists("config.ini")){
	echo "Nincs meg a konfigurációs fájl!";
	exit();
}
$config=parse_ini_file("config.ini", true, INI_SCANNER_TYPED);
//require_once("config.php");

// include composer autoload
require 'vendor/autoload.php';

// import the Intervention Image Manager Class
use Intervention\Image\ImageManager;
 
// Ha 0, akkor FELÜLÍRJA a létező fájlt
define('RENAME_F', 1); 
 
/** 
 * Fáljnév beállítása
 * Ha a fájl létezik és a RENAME_F értéke 1, akkor beállítjuk "img_name_1"-re
 * 
 * $p = mappa elérési út, $fn=ellenőrizendő fájlnév, $ex=kiterjesztés $i=átnevezendő index
 */ 
function setFName($p, $fn, $ex, $i){ 
    if(RENAME_F ==1 && file_exists($p .$fn .$ex)){ 
        return setFName($p, F_NAME .'_'. ($i +1), $ex, ($i +1)); 
    }else{ 
        return $fn .$ex; 
    } 
} 
 
$re = '';
$redirect="history.back()";

if(isset($_FILES['upload']) && strlen($_FILES['upload']['name']) > 1) {
 
    define('F_NAME', preg_replace('/\.(.+?)$/i', '', basename($_FILES['upload']['name'])));   
 
    // Fájlnév kinyerése kiterjesztés nélkül
    $sepext = explode('.', strtolower($_FILES['upload']['name'])); 
    $type = end($sepext);    /** ez a kiterjesztés **/ 
    
	if(array_key_exists("images", $config)){
		$imgConfig=$config["images"];
		$allowedTypes=$imgConfig["img_set.type"];
		$maxw=$imgConfig['img_set.maxwidth'];
		$maxh=$imgConfig['img_set.maxheight'];
		$minw=$imgConfig['img_set.minwidth'];
		$minh=$imgConfig['img_set.minheight'];
		$maxSizeV=$imgConfig['img_set.maxsize.value'];
		$maxSizeU=$imgConfig['img_set.maxsize.unit'];
		$exp=$imgConfig["power_set.".$maxSizeU];
		$thumbMaxw=$imgConfig["img_thumbs.maxwidth"];
		$thumbMaxh=$imgConfig["img_thumbs.maxheight"];
		
		// Feltöltési mappa
		$upload_dir = in_array($type, $allowedTypes) ? $imgConfig["upload_dir.img"] : $imgConfig["upload_dir.audio"]; 
		//$upload_dir = trim($upload_dir, '/') .'/';
		
		// Validáljuk a fájltípust
		if(in_array($type, $allowedTypes)){
			// Kép szélesség x magasság
			list($width, $height) = getimagesize($_FILES['upload']['tmp_name']); 
	 
			if(isset($width) && isset($height)) { 
				if($width > $maxw || $height > $maxh){ 
					$re .= '\\n Szélesség x Magasság = '. $width .' x '. $height .' \\n A maximum Szélesség x Magasság ennyi lehet: '. $maxw. ' x '. $maxh; 
				} 
	 
				if($width < $minw || $height < $minh){ 
					$re .= '\\n Szélesség x Magasság = '. $width .' x '. $height .'\\n A minimum Szélesség x Magasság ennyi lehet: '. $minw. ' x '. $minh; 
				} 
	 
				if($_FILES['upload']['size'] > $maxSizeV*pow(1024,$exp)){
					$re .= '\\n A feltölteni kívánt fájl mérete: '.($_FILES['upload']['size']/1024).' KB.\\nA maximális fájlméret ennyi lehet: '. $maxSizeV. ' '.$maxSizeU.'.'; 
				}
			} 
		}else{
			$re .= 'A fájl: <i>'. $_FILES['upload']['name']. '</i> nem engedélyezett kiterjesztéssel rendelkezik!'; 
		}
		 
		// Fájl feltöltési mappa
		if(substr($upload_dir, 0, 1)=="/")
			$path=$_SERVER['DOCUMENT_ROOT'].$upload_dir;
		else
			$path=$upload_dir;
			
		$f_name = setFName($path, F_NAME, ".$type", 0);
		//$f_name = setFName($upload_dir, F_NAME, ".$type", 0); 
		$uploadpath = $path . $f_name;
		$thumbUrl=$imgConfig["upload_dir.thumb"].$f_name;
		(isset($_SERVER['HTTPS'])) ? $protocol="https://" : $protocol="http://";
		if(substr($upload_dir, 0, 1)=="/")
			$thumbUrl=$_SERVER['DOCUMENT_ROOT'].$thumbUrl;
	}
	else $re.='Hiba: Nincs a képekre vonatkozó beállítás elmentve!<br />';
	
	// Ha nincs hiba, töltsük föl a képet, egyébként írassuk ki a hibákat
    if($re == ''){ 
        if(move_uploaded_file($_FILES['upload']['tmp_name'], $thumbUrl)) { 
            $CKEditorFuncNum = $_GET['CKEditorFuncNum'];
			$displayUrl = $upload_dir.$f_name;
			
			if(substr($upload_dir, 0, 1)=="/")
				$displayUrl=$protocol.$_SERVER['SERVER_NAME'].$imgConfig["upload_dir.thumb"].$f_name;
			
			$manager = new ImageManager(['driver' => 'gd']);
			//eredeti kép
			$image = $manager->make($thumbUrl);
			$image->resize($thumbMaxw, $thumbMaxh, function($attr){
				$attr->aspectRatio();
				$attr->upsize();
			});
			
			$image->save($thumbUrl);
            $msg = F_NAME .'.'. $type .' sikeresen feltöltve: \\n- Méret: '. number_format($_FILES['upload']['size']/1024, 2, '.', '') .' KB'; 
            $re = in_array($type, $allowedTypes) ? "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$displayUrl', '$msg')</script>" : '<script>var cke_ob = window.parent.CKEDITOR; for(var ckid in cke_ob.instances) { if(cke_ob.instances[ckid].focusManager.hasFocus) break;} cke_ob.instances[ckid].insertHtml(\' \', \'unfiltered_html\'); alert("'. $msg .'"); var dialog = cke_ob.dialog.getCurrent();dialog.hide();</script>'; 
        }else{
            $re = '<script>alert("A fájlfeltöltés sikertelen!")</script>'; 
        } 
    }else{
        $re = '<script>alert("'. $re .'")</script>';
    }
}
 
// Rendereljük a HTML kimenetet 
@header('Content-type: text/html; charset=utf-8'); 

echo $re;
?>