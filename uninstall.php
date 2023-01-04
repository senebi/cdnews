<?php
	require("header.php");
?>
<h2>Eltávolítás</h2>
<?php
	$dbConfig=$config["database_connection"];
	$dropSql="drop database ".$dbConfig["db_name"];
	$mydb->runQuery($dropSql);
?>
<p>Sikeres eltávolítás.</p>
<a href="index.php">Vissza</a>
<?php
	include("footer.php");
?>