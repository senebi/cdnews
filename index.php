<?php
	require("header.php");
	if(!$mydb->checkIfExists()){
		header("location: install_form.php");
		exit();
	}
	
	include("main.php");
	
	include("footer.php");
?>