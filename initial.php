<?php
	@session_start();
	
	$configFile=dirname(__FILE__).DIRECTORY_SEPARATOR."config.ini";
	if(!file_exists($configFile)){
		echo "A konfigurációs fájl nem található!";
		exit();
	}
	$config=parse_ini_file($configFile, true, INI_SCANNER_TYPED);
	
	require_once("classes/mydb.class.php");
	require_once("classes/user.class.php");
	require_once("classes/entry.class.php");
	require_once("utility.php");
	
	if(!isset($mydb))
		$mydb=new mydb($config);

	$allowedUrls=array("blog.php", "list_entries.php");
	if(in_array(basename($_SERVER['SCRIPT_FILENAME']), $allowedUrls)){
		$id=null;
	
		if(isset($_GET["id"]))
			$id=$mydb->sanitize($_GET["id"]);
		
		$entry=new entry($id, $config);
	}
	
	$exceptions=array("install_form.php", "install_action.php");
	if(!$mydb->checkIfExists() && !in_array(basename($_SERVER["PHP_SELF"]),$exceptions)){
		header("location: install_form.php");
		exit();
	}
	
	if(!isset($user))
		$user=new user($config);

	if(isset($_GET["action"])){
		if($_GET["action"]=="logout")
			$user->logout();
	}
?>