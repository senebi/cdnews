<?php
	require("header.php");
	//teszt login: admin;teszt
	
?>
<h2>Belső funkciók</h2>
<?php
	echo $user->getMessage()."<br />";
	if(!$user->isLoggedIn()){
		header("location: login_form.php");
		exit();
	}
	if(isset($_POST["redirect"])){
		if(file_exists($_POST["redirect"].".php"))
			header("location: ".$_POST["redirect"].".php");
	}
?>
<p>Mostantól elérheted a <a href="add_entry_form.php">Bejegyzés hozzáadása</a> funkciót.</p>
<p>Vagy <a href="list_entries.php">nézd meg</a> a már létező bejegyzéseket</p>
<?php
	include("footer.php");
?>