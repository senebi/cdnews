<?php
	require("header.php");
	//teszt login: haruko;teszt
	if($user->isLoggedIn()){
		$location="login_action.php";
		if(isset($_POST["redirect"])){
			if(file_exists($_POST["redirect"].".php"))
				$location=$_POST["redirect"].".php";
		}
		header("location: ".$location);
		exit();
	}
?>
<h2>Bejelentkezés</h2>
<?php
	echo $user->getMessage();
?>
<form method="post" action="login_form.php">
	<label for="login_user">Felhasználónév:</label>
	<input type="text" name="login_user" required />
	<span>
	<?php
		displayError();
	?>
	</span><br />
	<label for="login_pass">Jelszó:</label>
	<input type="password" name="login_pass" required />
	<span>
	<?php
		displayError();
	?>
	</span>
	<?php
		if(isset($_GET["redirect"])){
	?>
	<input type="hidden" name="redirect" value="<?php echo $_GET["redirect"]; ?>" />
	<?php
		}
	?>
	<p>
		<input type="submit" name="btn_login" value="Bejelentkezés" />
	</p>
</form>
<?php
	include("footer.php");
?>