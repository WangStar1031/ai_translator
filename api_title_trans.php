
<?php
	require_once 'library/trans_lib.php';
	require_once 'library/common_lib.php';
	header('Content-Type: text/html; charset=utf-8');
	if(isset($_POST['category'])){
		$cat = $_POST['category'];
		switch ($cat) {
			case 'FullSave':
				$jpn = $_POST['jpn_title'];
				$jpn = str_replace("。", "", $jpn);
				$eng = $_POST['eng_title'];
				$pos = "";
				insert_new_sentence($jpn, $eng, $pos);
				break;
			case 'PatternSave':
				$jpn = $_POST['jpn_sentence'];
				$eng = $_POST['eng_sentence'];
				$pos = $_POST['pattern'];
				insert_new_sentence($jpn, $eng, $pos);
				break;
			case 'WordSave':
				$jpn = $_POST['jpn_word'];
				$eng = $_POST['eng_word'];
				insert_new_word($jpn, $eng);
				break;
		}
	}
	$_jpn_title = '';
	if(isset($_GET['TITLE']))$_jpn_title = $_GET['TITLE'];
	if(isset($_POST['TITLE']))$_jpn_title = $_POST['TITLE'];
	if($_jpn_title != ""){
		$_jpn_title = $_GET['TITLE'];
		$patterns = get_word_patterns(false);
		// print_r($patterns);
		$sentences = get_sentence_patterns(false);
		$eng = process_individual( $_jpn_title);
		// $pattern = search_pattern_id($_jpn_title, $sentences, process_individual_sentence( $_jpn_title));
		// print_r($pattern);
		// $eng = post_pattern_process($eng, $pattern, $eng);
		// print_r($eng);
	?>
<script src="assets/js/jquery.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<style type="text/css">
		body{ padding: 8px; }
		table, input{ width: 100%; }
		.pattern, .words{ display: none; }
	</style>
	<form method="POST">
		<input type="hidden" name="category">
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
			<!-- <div class="btn btn-success" onclick="PatternClicked()">Pattern</div> -->
			<!-- <div class="btn btn-success" onclick="WordsClicked()">Words</div> -->
			<div class="btn btn-primary" onclick="FullSave()">Save</div>
		</form-group>
		<div class="col-lg-12">
			<div class="row pattern">
				<div class="col-lg-12">
					<table>
						<tr>
							<td>Japanese</td>
						</tr>
						<tr>
							<td>
								<textarea style="width: 100%;" rows="3" name="jpn_sentence"></textarea>
							</td>
						</tr>
						<tr>
							<td>English</td>
						</tr>
						<tr>
							<td>
								<textarea style="width: 100%;" rows="3" name="eng_sentence"></textarea>
							</td>
						</tr>
						<tr>
							<td>Position</td>
						</tr>
						<tr>
							<td>
								<input type="text" name="pattern">
							</td>
						</tr>
					</table>
					<div class="btn btn-primary" onclick="PatternSave()">Save</div>
				</div>
			</div>
		</div>
		<div class="col-lg-12">
			<div class="row words">
				<div class="col-lg-12">
					<table>
						<tr>
							<td>Japanese</td>
						</tr>
						<tr>
							<td>
								<input type="text" name="jpn_word">
							</td>
						</tr>
						<tr>
							<td>English</td>
						</tr>
						<tr>
							<td>
								<input type="text" name="eng_word">
							</td>
						</tr>
					</table>
					<div class="btn btn-primary" onclick="WordSave()">Save</div>
				</div>
			</div>
		</div>
	</form>

	<?php
	}
?>

<script type="text/javascript">
	function PatternClicked(){
		$(".pattern").show();
		$(".words").hide();
	}
	function WordsClicked(){
		$(".words").show();
		$(".pattern").hide();
	}
	function FullSave(){
		$("input[name='category']").val("FullSave");
		$("form").submit();
	}
	function PatternSave(){
		$("input[name='category']").val("PatternSave");
		$("form").submit();
	}
	function WordSave(){
		$("input[name='category']").val("WordSave");
		$("form").submit();
	}
</script>