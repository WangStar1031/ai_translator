<?php
	$category = 1;
	if( isset($_POST['category'])) $category = $_POST['category'];
	if( isset($_GET['category'])) $category = $_GET['category'];
	$cat_Date = "";
	if( isset($_POST['date'])) $cat_Date = $_POST['date'];
	if( isset($_GET['date'])) $cat_Date = $_GET['date'];
?>
<form class="categorySelect" style="padding: 15px;">
	<input type="hidden" name="category" id="category">
	<input type="hidden" name="date" id="date" value="<?= $cat_Date;?>">
	<button class="btn btn-default <?= ($category==0?'btn-success':'')?>" onclick="document.getElementById('category').value = 0;">Dictionary</button>
	<button class="btn btn-default <?= ($category==1?'btn-success':'')?>" onclick="document.getElementById('category').value = 1;">Content</button>
	<button class="btn btn-default <?= ($category==2?'btn-success':'')?>" onclick="document.getElementById('category').value = 2;">Product</button>
</form>
