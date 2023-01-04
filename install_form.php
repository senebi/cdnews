<?php
	require("initial.php");
	if($mydb->checkIfExists()){
		header("location: index.php");
		exit();
	}
?>
<h2>Telepítés</h2>
<p>Az alábbi űrlapon add meg a legfontosabb adatokat.</p>
<?php
	$constantWarning=" (Csak újraindítás után lép érvénybe!)";
	$dbConfig=$config["database_connection"];
?>
<form method="post" action="install_action.php">
	<p>Először az adatbázishoz való kapcsolódás adatait adjuk meg:</p>
	<label for="input_dbhost">Szerver (host): </label><br />
	<input type="text" name="input_dbhost" value="<?php echo $dbConfig["host_name"]; ?>" /><?php //echo $constantWarning; ?><br />
	<label for="input_dbuser">Adatbázis felhasználónév: </label><br />
	<input type="text" name="input_dbuser" value="<?php echo $dbConfig["user_name"]; ?>" /><?php //echo $constantWarning; ?><br />
	<label for="input_dbpass">Adatbázis jelszó: </label><br />
	<input type="password" name="input_dbpass" value="<?php echo $dbConfig["password"]; ?>" /><?php //echo $constantWarning; ?><br />
	<label for="input_dbname">Adatbázis név: </label><br />
	<input type="text" name="input_dbname" value="<?php echo $dbConfig["db_name"]; ?>" /><?php //echo $constantWarning; ?><br />
	<hr />
	<p>Majd az egyéb felhasználói adatok következnek:</p>
	<label for="table_prefix">Adatbázis táblanév előtag (prefix): </label><br />
	<input type="text" name="table_prefix" value="<?php echo $dbConfig["table_prefix"]; ?>" /><br />
	<label for="admin_user">Felhasználónév: </label><br />
	<input type="text" name="admin_user" placeholder="pl. admin" required /> (a jelszót később, külön felületen kell megadni)<br />
	<span>
		<?php
			displayError();
		?>
	</span>
	
	<p><input type="submit" name="btn_install" value="Telepítés" /></p>
</form>
<?php
	include("footer.php");
?>