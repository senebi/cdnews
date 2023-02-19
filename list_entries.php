<?php
	$included=strtolower(realpath(__FILE__))!=strtolower(realpath($_SERVER["SCRIPT_FILENAME"]));
	
	if($included)
		require("initial.php");
	else
		require("header.php");
	
?>
<h2>Bejegyzések</h2>
<?php
	if(!isset($_GET["id"]))
		echo "<p>".$user->getMessage()."</p>";
	if($user->isLoggedIn()){
?>
		<a href="add_entry_form.php">Bejegyzés hozzáadása</a>
<?php
	}

	if($entry->isEntryLoaded()){
	?>
		<p>Szerző: <?php echo $entry->get("author"); ?>, kategória: <?php echo $entry->get("category"); ?>, dátum: <?php echo $entry->get("publishDate"); ?><br />
		<?php echo $entry->get("title"); ?></p><hr /><?php echo $entry->get("content"); ?>
		<div class="clearfix"></div>
	<?php
		if($user->isLoggedIn()){
	?>
		<ul>
			<li><a href="edit_entry_form.php?id=<?php echo $entry->get("id"); ?>">szerkesztés --></a></li>
			<li><a href="delete_entry_action.php?id=<?php echo $entry->get("id"); ?>">törlés --></a></li>
		</ul>
	<?php
		}
		?>
		<!-- Load Facebook SDK for JavaScript -->
		<div id="fb-root"></div>
		<script async defer crossorigin="anonymous" src="https://connect.facebook.net/hu_HU/sdk.js#xfbml=1&version=v15.0" nonce="j4gH2Rc4"></script>
		
	<?php
		(isset($_SERVER['HTTPS'])) ? $protocol="https://" : $protocol="http://";
		$urlToShare=$protocol.$_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI'];
	?>
		<!-- Fb megosztás gomb megjelenítése -->
		<!-- Figyelem! Be van égetve az egyik paraméterbe a megosztani kívánt URL! -->
		<div class="fb-share-button" data-href="<?php echo $urlToShare; ?>" data-layout="button" data-size="large"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.chuushindojo.hu%2Ffrontend%2Fpages%2Fblog.php%3Fid%3D<?php echo $_GET["id"]; ?>&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Megosztás</a></div>
		<p><a href="<?php echo $_SERVER["PHP_SELF"]; ?>"><-- vissza</a></p>
	<?php
	}
	else{ //a bejegyzés nincs betöltve
		if($id!=null)
			echo "<p>A megadott (<i>$id</i>) azonosítóval rendelkező bejegyzés nem található!</p>";
		else{
			/*
			public function readAll($maxColUnits, $columnSizes, $rowClasses="")
			int $maxColUnits: a maximum kitölthető oszlopszámot adja meg Bootstrap egységekben 1-12 értékekkel (teljes sor szélessége)
			array $columnSizes: egy oszlop kívánt szélessége különböző szélességű kijelzőkön
			string $rowClasses: további osztályok a sorokhoz, alapértelmezettként üres ("")
			Példák méretekre:
			["md" => 3, "sm" => 12] --> "col-md-3 col-sm-12"
			[12, "sm" => 6] --> "col-12 col-sm-6"
			*/
			$list=$entry->readAll(9, ["md" => 3, "sm" => 12], "justify-content-center");
			echo $list;
		}
	}

	include("footer.php");
?>