function forgotPassword(){
	alert("forgot Password");
}

function hasLowerCase(str) {
	return (/[a-z]/.test(str));
}
function hasUpperCase(str) {
	return (/[A-Z]/.test(str));
}
function hasSpecialChr(str){
	return (/[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(str));
}
function hasNumberChr(str){
	return (/[0-9]/.test(str));
}
function has8Chrs(str){
	return str.length >= 8;
}
function NewPassChange(){
	var strPass = $("input[name=newPass]").val();
	if( hasLowerCase(strPass)){
		$(".LowerCaseTd .fa-check-circle-o").removeClass("HideItem");
		$(".LowerCaseTd .fa-dot-circle-o").addClass("HideItem");
	} else{
		$(".LowerCaseTd .fa-check-circle-o").addClass("HideItem");
		$(".LowerCaseTd .fa-dot-circle-o").removeClass("HideItem");
	}
	if( hasUpperCase(strPass)){
		$(".UpperCaseTd .fa-check-circle-o").removeClass("HideItem");
		$(".UpperCaseTd .fa-dot-circle-o").addClass("HideItem");
	} else{
		$(".UpperCaseTd .fa-check-circle-o").addClass("HideItem");
		$(".UpperCaseTd .fa-dot-circle-o").removeClass("HideItem");
	}
	if( hasSpecialChr(strPass)){
		$(".SpecChrTd .fa-check-circle-o").removeClass("HideItem");
		$(".SpecChrTd .fa-dot-circle-o").addClass("HideItem");
	} else{
		$(".SpecChrTd .fa-check-circle-o").addClass("HideItem");
		$(".SpecChrTd .fa-dot-circle-o").removeClass("HideItem");
	}
	if( hasNumberChr(strPass)){
		$(".NumberCaseTd .fa-check-circle-o").removeClass("HideItem");
		$(".NumberCaseTd .fa-dot-circle-o").addClass("HideItem");
	} else{
		$(".NumberCaseTd .fa-check-circle-o").addClass("HideItem");
		$(".NumberCaseTd .fa-dot-circle-o").removeClass("HideItem");
	}
	if( has8Chrs(strPass)){
		$(".StrLenCaseTd .fa-check-circle-o").removeClass("HideItem");
		$(".StrLenCaseTd .fa-dot-circle-o").addClass("HideItem");
	} else{
		$(".StrLenCaseTd .fa-check-circle-o").addClass("HideItem");
		$(".StrLenCaseTd .fa-dot-circle-o").removeClass("HideItem");
	}
}