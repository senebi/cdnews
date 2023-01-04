<?php
	require("header.php");
?>
<h2>Felhasználó hozzáadása</h2>
<?php
	echo $user->getMessage()."<br />";
	if(!$user->isLoggedIn()){
		header("location: login_form.php");
		exit();
	}
?>
<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
	<label for="uj_user">Felhasználónév: </label>
	<input type="text" name="uj_user" placeholder="Szöveg, szám és _ jel lehet." required /><br />
	<span>
	<?php
		displayError();
	?>
	</span>
	<p><input type="submit" name="btn_add_user" value="Létrehozás" /></p>
</form>
<br />
<?php
	include_once("add_user_action.php");
	
	if(isset($_POST["btn_add_user"])){
		if(isset($msg))
			echo $msg;
	}

	include("footer.php");
?>