<?php
	require("header.php");
	
	if(!isset($_GET["user"]) && !isset($_POST["btn_savepass"])){
		header("location: users.php");
		exit();
	}
	if(isset($_POST["btn_savepass"])){
		$pass1=$mydb->sanitize($_POST["pass"]);
		$pass2=$mydb->sanitize($_POST["passRepeat"]);
		$userinput=$mydb->sanitize($_POST["user"]);
		
		if($pass1=="" || $pass2==""){
			header("location: addpass.php?user=".$userinput."&error=emptyField");
			exit();
		}
		if($pass1!=$pass2){
			header("location: addpass.php?user=".$userinput."&error=passwordsDontMatch");
			exit();
		}
		
	}
?>
<h2>Jelszó létrehozása</h2>
<?php
	echo $user->getMessage();
	
	if(isset($_POST["btn_savepass"])){
		$table_prefix=$config["database_connection"]["table_prefix"];
		$updateSql="update ".$table_prefix."felhasznalok set pass='".password_hash($pass1, PASSWORD_DEFAULT)."'
		where user='".$userinput."'";
		//jelszó hozzáadása
		$mydb->runQuery($updateSql);
		echo "<br />A jelszó sikeresen hozzáadva.";
		exit();
	}
?>
<form method="post" action="addpass.php">
	<fieldset>
		<legend>Vizsgált felhasználó</legend>
		Felhasználónév: <i>
		<?php
			$uservalue=(isset($_GET["user"])) ? $mydb->sanitize($_GET["user"]) : $userinput;
			echo $uservalue;
		?></i>
		<input type="hidden" name="user" value="<?php echo $uservalue; ?>" /><br />
		<label for="pass">Jelszó: </label>
		<input type="password" name="pass" required />
		<span>
		<?php
			if(isset($pass1) && $pass1=="") displayError();
		?>
		</span>
		<br />
		<label for="passRepeat">Jelszó újra: </label>
		<input type="password" name="passRepeat" required />
		<span>
		<?php
			displayError();
		?>
		</span>
		<p>
			<input type="submit" name="btn_savepass" value="Mentés" />
		</p>
	</fieldset>
</form>
<?php
	include("footer.php");
?>