<?php
	$arrRoles = [ROLE_STEWARD, ROLE_NEWS, ROLE_GPMODELS, ROLE_MIZUHOFX, ROLE_WATARI, ROLE_SAKE, ROLE_NDK];
	$arrClass = ["jraStewardReports", "jraNewsReports", "gbpModels", "MizuhoFX", "Watami", "Sake Brewery", "NDK Reports"];
	$arrHrefs = ["jra_stewards_reports.php", "jra_news_reports.php", "#", "#", "#", "#", "#"];
	$arrTitle = ["JRA Stewards", " JRA News", "GP Models", "Mizuho FX", "Watami", "Sake Brewery", "NDK Reports"];
?>

<div class="col-lg-12">
	<div class="reports row">
		<?php
		for( $i = 0; $i < count($arrRoles); $i++){
			if( ($userRole & $arrRoles[$i]) == $arrRoles[$i]){
		?>
			<div class="<?= $arrClass[$i];?> col-sm-4">
				<a href="<?= $arrHrefs[$i];?>" target="_blank">
					<p><?= $arrTitle[$i];?></p>
				</a>
			</div>
		<?php
			}
		}
		?>
	</div>
</div>