<?php
	$allUsers = getAllUsersExceptMe($userName);
?>
<link rel="stylesheet" type="text/css" href="assets/css/userMan.css?<?= time();?>">
<div class="userManage">
	<input type="hidden" name="defaultRole" value="<?= ROLE_DEFAULT_USER?>">
	<input type="hidden" name="userName" value="<?= $userName?>">
	<table>
		<tr>
			<th colspan="2">N<u>o</u></th>
			<th>User Name</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Email</th>
			<th>Stewards</th>
			<th>News</th>
			<th>GP models</th>
			<th>Mizuho FX</th>
			<th>Watari</th>
			<th>Sake</th>
			<th>NDK</th>
			<th>Invite User</th>
		</tr>
	<?php
		for ($i=0; $i < count($allUsers); $i++) {
			$user = $allUsers[$i];
			$userId = $user['Id'];
			echo "<tr class='userInfos ID_" . $userId . "' id='" . $userId . "'>";
				echo "<td><input type='checkbox'></td>";
				echo "<td>" . ( $i + 1 ) . "</td>";
				echo "<td>" . $user['nickname'] . "</td>";
				echo "<td>" . $user['firstname'] . "</td>";
				echo "<td>" . $user['lastname'] . "</td>";
				echo "<td>" . $user['email'] . "</td>";
				$role = $user['role'];
				$_steward = ($role & ROLE_STEWARD) == ROLE_STEWARD;
				$_news = ($role & ROLE_NEWS) == ROLE_NEWS;
				$_gpmodels = ($role & ROLE_GPMODELS) == ROLE_GPMODELS;
				$_mizuhofx = ($role & ROLE_MIZUHOFX) == ROLE_MIZUHOFX;
				$_watari = ($role & ROLE_WATARI) == ROLE_WATARI;
				$_sake = ($role & ROLE_SAKE) == ROLE_SAKE;
				$_ndk = ($role & ROLE_NDK) == ROLE_NDK;
				$_invite = ($role & ROLE_INVITE) == ROLE_INVITE;
				echo "<td class='Stewards " . ($_steward == true ? "chkImg" : "unChkImg") . "' onclick='onSteward(". $userId . ")'>" . "</td>";
				echo "<td class='News " . ($_news == true ? "chkImg" : "unChkImg") . "' onclick='onNews(". $userId . ")'>" . "</td>";
				echo "<td class='GPModels " . ($_gpmodels == true ? "chkImg" : "unChkImg") . "' onclick='onGPModels(". $userId . ")'>" . "</td>";
				echo "<td class='MizuhoFX " . ($_mizuhofx == true ? "chkImg" : "unChkImg") . "' onclick='onMizuhofx(". $userId . ")'>" . "</td>";
				echo "<td class='Watari " . ($_watari == true ? "chkImg" : "unChkImg") . "' onclick='onWatari(". $userId . ")'>" . "</td>";
				echo "<td class='Sake " . ($_sake == true ? "chkImg" : "unChkImg") . "' onclick='onSake(". $userId . ")'>" . "</td>";
				echo "<td class='NDK " . ($_ndk == true ? "chkImg" : "unChkImg") . "' onclick='onNdk(". $userId . ")'>" . "</td>";
				echo "<td class='Invite " . ($_invite == true ? "chkImg" : "unChkImg") . "' onclick='onInvite(". $userId . ")'>" . "</td>";
			echo "</tr>";
		}
	?>
	</table>
	<div class="form-group">
		<div class="btn btn-primary btnSave" onclick="btnSaveClicked()">
			Save
		</div>
		<div class="btn btn-primary btnDelete" onclick="btnDeleteClicked()">
			Delete
		</div>
		<div class="btn btn-primary inviteUser" data-toggle="modal" data-target="#inviteModal">
			Invite User
		</div>
	</div>
	<form class="modal fade" id="inviteModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="Id" style="color: black;">Invite New User</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">x</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="firstname" class="col-form-label" style="color: gray;">First Name</label>
						<input type="text" name="firstname" class="form-control" >
						<label for="lastname" class="col-form-label" style="color: gray;">Last Name</label>
						<input type="text" name="lastname" class="form-control" >
						<label for="email" class="col-form-label" style="color: gray;">Email Address</label>
						<input type="email" name="email" class="form-control" >
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button id="btn_new_word_save" type="button" class="btn btn-primary" onclick="confirmClicked()">Confirm</button>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	var userName = $("input[name='userName']").val();
	var g_defaultRole = $("input[name='defaultRole']").val() * 1;
</script>
<script type="text/javascript" src="assets/js/userMan.js?<?= time()?>"></script>