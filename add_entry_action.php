<?php
	require("header.php");
	
	if(!isset($_POST["btn_add_entry"])){
		$location="add_entry_form.php";
		if(!$user->isLoggedIn()) $location="login_form.php";
		header("location: ".$location);
		exit();
	}
	
	//ha van bejegyzés (POST), akkor az objektum létrehozásakor be is töltődnek az adatok
	$entry=new entry(null, $config);
	//add() fv. kimenti adatbázisba
	$entry->add();
?>