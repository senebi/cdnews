<?php
	require("initial.php");
?>
<!DOCTYPE html>
<html lang="hu" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<link type="text/css" href="css/contents.css" rel="stylesheet" media="screen" />
<?php
	if(basename($_SERVER['SCRIPT_FILENAME'])=="list_entries.php"){
?>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
<?php
		if($entry->isEntryLoaded()){
			(isset($_SERVER['HTTPS'])) ? $protocol="https://" : $protocol="http://";
			$urlToShare=$protocol.$_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI'];
		?>
			<meta property="og:url"                content="<?php echo $urlToShare; ?>" />
			<meta property="og:type"               content="article" />
			<meta property="og:title"              content="<?php echo $entry->get("title"); ?>" />
			<meta property="og:description"        content="<?php echo $entry->truncate_words(strip_tags(htmlspecialchars_decode($entry->get("content"))),8); ?>" />
			<meta property="og:image"              content="<?php echo $entry->getFirstImage("html", htmlspecialchars_decode($entry->get("content")), "url"); ?>" />
		<?php
		}
	}
?>
	<title>Chuushin dojo news</title>
</head>
<body>
	<ul>
		<li><a href="index.php">Kezdőlap</a></li>
		<li><a href="users.php">Felhasználók</a></li>
		<li><a href="list_entries.php">Bejegyzések</a></li>
		<!--<li><a href="teszt.php">Teszt</a></li>-->
	<?php
		if(!$user->isLoggedIn()){
	?>
			<li><a href="login_form.php">Bejelentkezés</a></li>
	<?php
		}
		else{
	?>
			<li><a href="index.php?action=logout">Kijelentkezés</a></li>
	<?php
		}
	if($mydb->checkIfExists()){
?>
	<!-- Teszteléshez megjegyzésbe tesszük, nehogy véletlen rákattintson valaki és elölről kelljen kezdeni a telepítést! -->
	<li><a href="uninstall.php">Eltávolítás</a></li>
<?php
	}
?>
	</ul>
	<h1>Chuushin dojo news</h1>