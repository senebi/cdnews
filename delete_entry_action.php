<?php
	require("header.php");
	
	if(!isset($_GET["id"])){
		$location="list_entries.php";
		if(!$user->isLoggedIn()) $location="login_form.php";
		header("location: ".$location);
		exit();
	}
	
	$id=$mydb->sanitize($_GET["id"]);
	
	$entry=new entry($id, $config);
	
	//bejegyzés törlése az adatbázisból
	if($entry->isEntryLoaded())
		$entry->delete();
	else echo "<p>Nincs mit törölni!</p>";
?>