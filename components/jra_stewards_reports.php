<?php	
	$events = glob( "logs/backup/*.event");
	$today_events = glob("*.event");
	$events = array_merge($events, $today_events);
	$cur_year = date("Y");
	$cur_month = date("n");
	if(isset($_POST["year"])) $cur_year = $_POST["year"];
	if(isset($_POST["month"])) $cur_month = $_POST["month"];
	$dtEvent = "";
	if( isset($_POST['date'])) $dtEvent = $_POST['date'];
	if( isset($_GET['date'])) $dtEvent = $_GET['date'];
	echo "<div class='col-lg-12'><h2>JRA Stewards Reports</h2></div>";
	switch ($category) {
		case 0:
			$db_name = "ai_trans";
			include __DIR__ . '/dictionary.php';
			break;
		case 1:
?>
<div class="col-lg-12">
	<div class="row">
		<div class="report-total col-lg-3">
			<form method=post id="frmList" name="frmList">
			<select name="year" id="year" onchange="javascript: frmList.submit();">
				<?php for($i=2017; $i<=date("Y"); $i++){?>
				<option value="<?=$i?>"<?php if($cur_year == $i) echo ' selected';?>><?=$i?></option>
				<?php }?>
			</select>
			/
			<select name="month" id="month" onchange="javascript: frmList.submit();">
				<?php for($i=1; $i<=12; $i++){?>
				<option value="<?=$i?>"<?php if($cur_month == $i) echo ' selected';?>><?=$i?></option>
				<?php }?>
			</select>
			</form>
		<?php
			foreach ($events as $event_data) {
				$event_data = substr($event_data, 12, 8);
				$event_date = substr($event_data,0,4).'-'.substr($event_data,4,2).'-'.substr($event_data,6,2);
				if($cur_year != date("Y", strtotime($event_date))) continue;
				if($cur_month != date("n", strtotime($event_date))) continue;
				echo '<a ' . (($dtEvent==$event_data) ? 'class="selected"': '') . 'href="jra_stewards_reports.php?category=1&date='.$event_data.'&lang=en">Report ('.date("F jS, Y", strtotime($event_date)).')</a><br>';
			}
		?>
	</div>
<?php
			if( $dtEvent != ""){
				$date_str = $dtEvent;
			?>
		<div class="report-contents col-lg-9">
			<?php
				include "components/jra_stewards_trans.php";
			?>
		</div>
	</div>
			<?php

			}
			?>
</div>
			<?php
			break;
		case 2:
?>
<div class="col-lg-12">
	<div class="row">
		<div class="report-total col-lg-3">
			<form method=post id="frmList" name="frmList">
			<select name="year" id="year" onchange="javascript: frmList.submit();">
				<?php for($i=2017; $i<=date("Y"); $i++){?>
				<option value="<?=$i?>"<?php if($cur_year == $i) echo ' selected';?>><?=$i?></option>
				<?php }?>
			</select>
			/
			<select name="month" id="month" onchange="javascript: frmList.submit();">
				<?php for($i=1; $i<=12; $i++){?>
				<option value="<?=$i?>"<?php if($cur_month == $i) echo ' selected';?>><?=$i?></option>
				<?php }?>
			</select>
			</form>
		<?php
			$link_href = "api_keiba_notice.php?c=14&d_val=";
			foreach ($events as $event_data) {
				$event_data = substr($event_data, 12, 8);
				$event_date = substr($event_data,0,4).'-'.substr($event_data,4,2).'-'.substr($event_data,6,2);
				if($cur_year != date("Y", strtotime($event_date))) continue;
				if($cur_month != date("n", strtotime($event_date))) continue;
				echo '<a ' . (($dtEvent==$event_data) ? 'class="selected"': '') . 'href="jra_stewards_reports.php?category=2&date='.$event_data.'">Report ('.date("F jS, Y", strtotime($event_date)).')</a><br>';
			}
			$curSelDate = substr($dtEvent,0,4).'-'.substr($dtEvent,4,2).'-'.substr($dtEvent,6,2);
			$pdf_File_name = date("F jS, Y", strtotime($curSelDate)) . " JRA Stewards Reports_en.pdf";
		?>
		</div>
		<div class="col-lg-9">
			<object data="jrastewards/<?= $pdf_File_name;?>" type="application/pdf" width="100%" height="100%">
			</object>
		</div>
	</div>
</div>
<?php
			break;
		default:
			break;
	}
?>