<?php
	require("header.php");
	require("classes/category.class.php");
?>
<h2>Bejegyzés létrehozása</h2>
<?php
	echo $user->getMessage()."<br />";
	if(!$user->isLoggedIn()){
		header("location: login_form.php");
		exit();
	}
	$cat=new category($config);
?>
<form method="post" action="add_entry_action.php">
	<label for="kateg">Kategória:</label>
	<select name="kateg">
	<?php
		echo $cat->read();
	?>
	</select>
	<br />
	<label for="uj_cim">Cím:</label>
	<input type="text" name="uj_cim" size=50 placeholder="Pár szóban írjuk le, miről szól a cikk." required /><br />
	<span>
	<?php
		displayError();
	?>
	</span>
	<label for="uj_tartalom">Tartalom:</label><br />
	<div class="centered">
		<textarea name="uj_tartalom" id="editor" required>
		</textarea>
	</div>
	<span>
	<?php
		displayError();
	?>
	</span>
	<p>
		<input type="submit" name="btn_add_entry" value="Létrehozás" />
	</p>
</form>
<script src="ckeditor/ckeditor.js"></script>
<script src="ckfinder/ckfinder.js"></script>
<script src="ckeditor/lang/hu.js"></script>

<script>
	//CKEditor 4-hez
	//initSample(); //alapértelmezett hívás a samples/js/sample.js fáljból
	var editor=CKEDITOR.replace('editor',{
		filebrowserUploadUrl: 'ck_upload.php',
		//filebrowserBrowseUrl: 'ck_browse.php',
        filebrowserUploadMethod: 'form'
	});
	//vagy filebrowserBrowseUrl (egyedi) van engedélyezve, vagy a CKFinder.setupCKEditor() (automata);
	CKFinder.setupCKEditor(editor);
</script>
<?php
	include("footer.php");
?>