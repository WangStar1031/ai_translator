
function onSteward(_Id){
	if( $(".ID_" + _Id).find(".Stewards").hasClass("chkImg") ){
		$(".ID_" + _Id).find(".Stewards").removeClass("chkImg").addClass("unChkImg");
		$(".ID_" + _Id).find(".Stewards").html("<img src='assets/imgs/unchecked.png'>");
	} else {
		$(".ID_" + _Id).find(".Stewards").removeClass("unChkImg").addClass("chkImg");
		$(".ID_" + _Id).find(".Stewards").html("<img src='assets/imgs/checked.png'>");
	}
}
function onNews(_Id){
	if( $(".ID_" + _Id).find(".News").hasClass("chkImg") ){
		$(".ID_" + _Id).find(".News").removeClass("chkImg").addClass("unChkImg");
		$(".ID_" + _Id).find(".News").html("<img src='assets/imgs/unchecked.png'>");
	} else {
		$(".ID_" + _Id).find(".News").removeClass("unChkImg").addClass("chkImg");
		$(".ID_" + _Id).find(".News").html("<img src='assets/imgs/checked.png'>");
	}
}
function onGPModels(_Id){
	if( $(".ID_" + _Id).find(".GPModels").hasClass("chkImg") ){
		$(".ID_" + _Id).find(".GPModels").removeClass("chkImg").addClass("unChkImg");
		$(".ID_" + _Id).find(".GPModels").html("<img src='assets/imgs/unchecked.png'>");
	} else {
		$(".ID_" + _Id).find(".GPModels").removeClass("unChkImg").addClass("chkImg");
		$(".ID_" + _Id).find(".GPModels").html("<img src='assets/imgs/checked.png'>");
	}
}
function onMizuhofx(_Id){
	if( $(".ID_" + _Id).find(".MizuhoFX").hasClass("chkImg") ){
		$(".ID_" + _Id).find(".MizuhoFX").removeClass("chkImg").addClass("unChkImg");
		$(".ID_" + _Id).find(".MizuhoFX").html("<img src='assets/imgs/unchecked.png'>");
	} else {
		$(".ID_" + _Id).find(".MizuhoFX").removeClass("unChkImg").addClass("chkImg");
		$(".ID_" + _Id).find(".MizuhoFX").html("<img src='assets/imgs/checked.png'>");
	}
}
function onWatari(_Id){
	if( $(".ID_" + _Id).find(".Watari").hasClass("chkImg") ){
		$(".ID_" + _Id).find(".Watari").removeClass("chkImg").addClass("unChkImg");
		$(".ID_" + _Id).find(".Watari").html("<img src='assets/imgs/unchecked.png'>");
	} else {
		$(".ID_" + _Id).find(".Watari").removeClass("unChkImg").addClass("chkImg");
		$(".ID_" + _Id).find(".Watari").html("<img src='assets/imgs/checked.png'>");
	}
}
function onSake(_Id){
	if( $(".ID_" + _Id).find(".Sake").hasClass("chkImg") ){
		$(".ID_" + _Id).find(".Sake").removeClass("chkImg").addClass("unChkImg");
		$(".ID_" + _Id).find(".Sake").html("<img src='assets/imgs/unchecked.png'>");
	} else {
		$(".ID_" + _Id).find(".Sake").removeClass("unChkImg").addClass("chkImg");
		$(".ID_" + _Id).find(".Sake").html("<img src='assets/imgs/checked.png'>");
	}
}
function onNdk(_Id){
	if( $(".ID_" + _Id).find(".NDK").hasClass("chkImg") ){
		$(".ID_" + _Id).find(".NDK").removeClass("chkImg").addClass("unChkImg");
		$(".ID_" + _Id).find(".NDK").html("<img src='assets/imgs/unchecked.png'>");
	} else {
		$(".ID_" + _Id).find(".NDK").removeClass("unChkImg").addClass("chkImg");
		$(".ID_" + _Id).find(".NDK").html("<img src='assets/imgs/checked.png'>");
	}
}
function onInvite(_Id){
	if( $(".ID_" + _Id).find(".Invite").hasClass("chkImg") ){
		$(".ID_" + _Id).find(".Invite").removeClass("chkImg").addClass("unChkImg");
		$(".ID_" + _Id).find(".Invite").html("<img src='assets/imgs/unchecked.png'>");
	} else {
		$(".ID_" + _Id).find(".Invite").removeClass("unChkImg").addClass("chkImg");
		$(".ID_" + _Id).find(".Invite").html("<img src='assets/imgs/checked.png'>");
	}
}

function confirmClicked(){
	var strEmail = $("input[name='email']").val();
	if( !strEmail){
		alert("No Email address.");
		return;
	}
	var strFirstName = $("input[name='firstname']").val();
	var strLastName = $("input[name='lastname']").val();
	var strFromUser = userName;
	jQuery.post("library/api_user_manager.php", 
		{inviteUser: strEmail, fromUser: strFromUser, firstName: strFirstName, lastName: strLastName
			, role: g_defaultRole},
		function(data){
			debugger;
			location.reload();
		}
	);
}
function getRoleCount(_Number){
	var retVal = 1;
	for( var i = 0; i < _Number; i++){
		retVal *= 2;
	}
	return retVal;
}
function btnSaveClicked(){
	var arrTrs = $("table tr.userInfos");
	for( var i = 0; i < arrTrs.length; i++){
		var curTr = arrTrs.eq(i);
		var arrTds = curTr.find("td");
		var curRole = 0;
		for( var j = 6; j < arrTds.length; j++){
			var curTd = arrTds.eq(j);
			var curRoleId = j - 6;
			if( curTd.hasClass("chkImg")){
				curRole = curRole*1+getRoleCount(curRoleId);
			}
		}
		console.log( curRole);
		jQuery.post("library/api_user_manager.php", 
			{userRole: curTr.attr("Id"), role: curRole}, function(data){
			console.log(data);
		});
	}
}
function btnDeleteClicked(){
	var arrTrs = $("table tr.userInfos");
	var arrCheckedIDs = [];
	for( var i = 0; i < arrTrs.length; i++){
		var curTr = arrTrs.eq(i);
		var checked = curTr.find("input[type='checkbox']").prop("checked");
		if( !checked)continue;
		arrCheckedIDs.push(curTr.attr("Id"));
	}
	if( arrCheckedIDs.length){
		jQuery.post("library/api_user_manager.php", 
			{deleteUser: arrCheckedIDs.join(",")}, function(data){
				location.reload();
		});
	}
}

$(function(){
	$(".userManage table td.chkImg").html("<img src='assets/imgs/checked.png'>");
	$(".userManage table td.unChkImg").html("<img src='assets/imgs/unchecked.png'>");
});
