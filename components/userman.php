<?php
	$allUsers = getAllUsersExceptMe($userName);
?>
<link rel="stylesheet" type="text/css" href="assets/css/userMan.css">
<div class="userManage">
	<table>
		<tr>
			<th>N<u>o</u></th>
			<th>User Name</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Email</th>
			<th>Invitation</th>
			<th>User Management</th>
		</tr>
	<?php
		for ($i=0; $i < count($allUsers); $i++) {
			$user = $allUsers[$i];
			echo "<tr>";

			echo "</tr>";
		}
	?>
	</table>
	<div class="btn btn-primary inviteUser" data-toggle="modal" data-target="#inviteModal">
		Invite User
	</div>
</div>

<div class="modal fade" id="inviteModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="Id" style="color: black;">Add New Word</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
	                <label for="source" class="col-form-label" style="color: gray;">Word (source)</label>
					<textarea class="form-control" name="source" id="source" rows="1" required=""></textarea>
				</div>
				<div class="form-group">
					<label for="destination" class="col-form-label" style="color: gray;">Word (target)</label>
					<textarea class="form-control" name="destination" id="destination" rows="1"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button id="btn_new_word_save" type="button" class="btn btn-primary" onclick="confirmClicked()">Confirm</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="assets/js/userMan.js"></script>