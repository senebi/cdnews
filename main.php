<h2>Köszöntelek!</h2>
<?php
	echo $user->getMessage();
?>
<p>
	Ez itt a cdnews hír nyilvántartó oldal, amely a Chuushin dojóhoz készült,
	de attól függetlenül is működik.
</p>
<p>
	A felhasználók listája itt megtalálható: <a href="users.php">Felhasználók</a>
</p>
<p>
	A bejegyzések (hírek) bárki számára megtekinthetők: <a href="list_entries.php">Bejegyzések</a>
</p>
<p>
	Ha új bejegyzést szeretnél hozzáadni, <?php if(!$user->isLoggedIn()){ ?><a href="login_form.php?redirect=add_entry_form">jelentkezz be</a>!<?php }
	else{
	?>
		kattints ide: <a href="add_entry_form.php">Bejegyzés hozzáadása</a>!
	<?php
	}
	?>
</p>