<h1>Men�plan-Upload</h1>
<form action="menu-upload.php" method="post" enctype="multipart/form-data">
	<label for="menu00">Men� 1</label>		<input type="file" name="menu[00]" accept="application/pdf" /><br/>
	<label for="menu01">Men� 2</label>		<input type="file" name="menu[01]" accept="application/pdf" /><br/>
	<label for="menu02">Men� 3</label>		<input type="file" name="menu[02]" accept="application/pdf" /><br/>
	<label for="menu03">Men� 4</label>		<input type="file" name="menu[03]" accept="application/pdf" /><br/>
	<label for="menu04">Men� 5</label>		<input type="file" name="menu[04]" accept="application/pdf" /><br/>
	<label for="menu05">Men� 6</label>		<input type="file" name="menu[05]" accept="application/pdf" /><br/>
	<input type="submit" name="submit" />
</form>
<p>
<?php
	require_once( '../config.php' );
	global $downloadDirectory;
	if ( !isset($_FILES['menu']) || !isset($downloadDirectory) ) {
		die();
	}
	$target_path = "../" . $downloadDirectory;
	for( $index = 0; $index < $numberOfMeals; $index++ ) {
		$index_str = '0' . $index;
		$index_plus1_str = '0' . ($index+1);
		$error = $_FILES['menu']['error'][$index_str];
		if ( $error == 0 ) {
			$file_path = $target_path . 'menu' . $index_plus1_str . '.pdf';
			move_uploaded_file($_FILES['menu']['tmp_name'][$index_str], $file_path);
			print_r('Upload von Speiseplan ' . ($index+1) . ' erfolgreich<br/>');
		}
	}
?>
</p>