<?php
	require("header.php");
	
	if(!isset($_POST["btn_update_entry"])){
		$location="add_entry_form.php";
		if(!$user->isLoggedIn()) $location="login_form.php";
		header("location: ".$location);
		exit();
	}
	
	$entry=new entry(null, $config);
	//módosítás adatbázisban
	$entry->update();
?>