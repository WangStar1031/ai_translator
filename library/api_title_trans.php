
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<?php
	// require_once __DIR__ . '/MySql.php';
	require_once __DIR__ . '/trans_lib.php';
	require_once __DIR__ . '/common_lib.php';
	$_jpn_title = '';
	if(isset($_GET['TITLE']))$_jpn_title = $_GET['TITLE'];
	if(isset($_POST['TITLE']))$_jpn_title = $_POST['TITLE'];
	if($_jpn_title != ""){
		$_jpn_title = $_GET['TITLE'];
		$patterns = get_word_patterns();
		$sentences = get_sentence_patterns();
		$eng = process_individual_sentence( $_jpn_title);
	?>
	<style type="text/css">
		body{ padding: 8px; }
		table, input{ width: 100%; }
		.word_pattern{ border: 1px solid gray; }
	</style>
	<div class="row">
		<div class="col-lg-12">
			<table>
				<tr>
					<td style="width: 100px;">Japanese Title</td>
				</tr>
				<tr>
					<td><input type="text" name="jpn_title" readonly value="<?=$_jpn_title?>"></td>
				</tr>
				<tr>
					<td>English Title</td>
				</tr>
				<tr>
					<td><input type="text" name="eng_title" value="<?= $eng?>"></td>
				</tr>
			</table>	
		</div>
		<form-group class="col-lg-12">
			<button>Save</button>
		</form-group>
		<div class="col-lg-6 word_pattern">
			<label>Japanese</label>
			<input type="text" name="jpn_word">
			<label>English</label>
			<input type="text" name="eng_word">
		</div>
	</div>
	<div class="row">
		
	</div>
	<?php
	}
?>