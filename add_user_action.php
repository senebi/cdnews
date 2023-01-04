<?php
	if(!isset($_POST["btn_add_user"])){
		$msg="Nincs adat (felhasználónév)!";
		$location="add_user_form.php";
		if(!$user->isLoggedIn()){
			$location="login_form.php";
			header("location: ".$location);
			exit();
		}
	}
	else{
		$user=$mydb->sanitize($_POST["uj_user"]);
		
		if($user==""){
			header("location: add_user_form.php?error=emptyField");
			exit();
		}
		
		$table_prefix=$config["database_connection"]["table_prefix"];
		$insertSql="insert into ".$table_prefix."felhasznalok (user) values('".$user."')";
		//felhasználó hozzáadása
		$mydb->runQuery($insertSql);
		$msg="A(z) <i>".$user."</i> nevű felhasználó sikeresen hozzáadva.";
	}
?>