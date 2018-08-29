
<div class="col-lg-12">
	<div class="reports row">
		<?php
		for( $i = 0; $i < count($arrRoles); $i++){
			if( ($userRole & $arrRoles[$i]) == $arrRoles[$i]){
		?>
			<div class="<?= $arrClass[$i];?> col-sm-4">
				<a href="<?= $arrHrefs[$i];?>?category=1">
					<p><?= $arrTitle[$i];?></p>
				</a>
			</div>
		<?php
			}
		}
		?>
	</div>
</div>