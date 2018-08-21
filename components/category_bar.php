<?php
	$category = 1;
	if( isset($_POST['category'])) $category = $_POST['category'];
	if( isset($_GET['category'])) $category = $_GET['category'];
?>
<form class="categorySelect" style="padding: 15px;">
	<input type="hidden" name="category" id="category">
	<button class="btn btn-default <?= ($category==0?'btn-success':'')?>" onclick="document.getElementById('category').value = 0;">Dictionary</button>
	<button class="btn btn-default <?= ($category==1?'btn-success':'')?>" onclick="document.getElementById('category').value = 1;">Content</button>
	<button class="btn btn-default <?= ($category==2?'btn-success':'')?>" onclick="document.getElementById('category').value = 2;">Product</button>
</form>
