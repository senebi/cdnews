<?php
	require("header.php");
	require("classes/category.class.php");
?>
<h2>Bejegyzés szerkesztése</h2>
<?php
	echo $user->getMessage()."<br />";
	if(!$user->isLoggedIn()){
		header("location: login_form.php");
		exit();
	}
	$cat=new category($config);
	$id=null;
	
	if(!isset($_GET["id"])){
		header("location: list_entries.php");
		exit();
	}
	else
		$id=$mydb->sanitize($_GET["id"]);
	
	$entry=new entry($id, $config);
?>
<form method="post" action="edit_entry_action.php">
	<label for="kateg">Kategória:</label>
	<select name="kateg">
	<?php
		echo $cat->read($id);
	?>
	</select>
	<br />
	<input type="hidden" name="editEntryID" value=<?php echo $id; ?> />
	<label for="cim">Cím:</label>
	<input type="text" name="cim" size=50 placeholder="Pár szóban írjuk le, miről szól a cikk." value="<?php echo $entry->get("title"); ?>" required /><br />
	<span>
	<?php
		displayError();
	?>
	</span>
	<label for="tartalom">Tartalom:</label><br />
	<div class="centered">
		<textarea name="tartalom" id="editor" required>
		<?php echo $entry->get("content"); ?>
		</textarea>
	</div>
	<span>
	<?php
		displayError();
	?>
	</span>
	<p>
		<input type="submit" name="btn_update_entry" value="Mentés" />
	</p>
	<p><a href="list_entries.php?id=<?php echo $id; ?>"><-- vissza</a></p>
</form>
<script src="ckeditor/ckeditor.js"></script>
<!--<script src="ckeditor/samples/js/sample.js"></script>-->
<script src="ckeditor/lang/hu.js"></script>

<script>
	//CKEditor 4-hez
	//initSample(); //alapértelmezett hívás a samples/js/sample.js fáljból
	CKEDITOR.replace('editor',{
		filebrowserUploadUrl: 'ck_upload.php',
        filebrowserUploadMethod: 'form'
	});
	
</script>
<?php
	include("footer.php");
?>