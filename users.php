<?php
	require("header.php");
?>
<h2>Felhasználók</h2>
<?php
	echo $user->getMessage()."<br />";
	if($user->isLoggedIn()){
?>
		<a href="add_user_form.php">Felhasználó hozzáadása</a>
<?php
	}
?>
<p>Azon felhasználók listáját látod, akik (még) nem rendelkeznek jelszóval, így bejelentkezni se tudnak.</p>
<?php
	echo $user->listUsers();

	include("footer.php");
?>