
function toggle_patterns(body_class) {
	jQuery(".manual_edit").remove();
	document.body.className = body_class;
	jQuery(".edit_on span.sentence_pattern").each(function(){
		jQuery(this).append('<div class="manual_edit">'+jQuery(this).attr("pattern_id")+'</div>');
	});
	jQuery("span.sentence_pattern").unbind("click");
	if(body_class == "edit_on") {
		jQuery(".edit_on span.sentence_pattern").click(function(){
			jQuery(this).children("div").remove();
			jQuery("#trans_source").prop("value", jQuery(this).attr("pattern_source"));
			jQuery(this).append('<div class="manual_edit">'+jQuery(this).attr("pattern_id")+'</div>');
			jQuery.post("library/trans_api.php?case=1001", {Id: jQuery(this).attr("pattern_id")}, function(data){
				pattern = JSON.parse(data);
				jQuery("#source").prop("value", pattern.jpn);
				jQuery("#destination").prop("value", pattern.eng);
				jQuery("#pos").prop("value", pattern.pos);
				jQuery("#Id").prop("value", pattern.Id);
				$("#addModal").modal("show");
			});
		});
	}
}
jQuery(function(){
	jQuery("#btn_save").click(function(){
		pattern = {
			"trans_source": jQuery("#trans_source").prop("value"),
			"jpn": jQuery("#source").prop("value"),
			"eng": jQuery("#destination").prop("value"),
			"pos": jQuery("#pos").prop("value"),
			"Id": jQuery("#Id").prop("value")
		}
		jQuery.post("library/trans_api.php?case=1002", {pattern: JSON.stringify(pattern)}, function(data){
			ret = JSON.parse(data);
			target_obj = jQuery(".edit_on span.sentence_pattern[pattern_id="+ret.Id+"]");
			target_obj.children("div").remove();
			target_obj.html(ret.result);
			target_obj.append('<div class="manual_edit">'+target_obj.attr("pattern_id")+'</div>');
			$("#addModal").modal("hide");
		});
	});

	jQuery("body").keydown(function(e){
		if(e.keyCode == 113) {
			if(document.body.className == "edit_on") toggle_patterns("edit_off");
			else toggle_patterns("edit_on");
		}
	});
});
function sendEmails(){
	jQuery.post("sendMail_attaches.php", {sendMail: jQuery("b").html(), type: 'en'}, function(data){
	});
}
function addCustomerEmail(){
	jQuery.post("library/clientEmails.php", {getCustomers: true}, function(data){
		ret = JSON.parse(data);
		var strHtml = "";
		for( var i = 0; i < ret.length; i++){
			var curInfo = ret[i];
			strHtml += '<tr><td><input type="text" value="'+ curInfo.firstName +'"></td><td><input type="text" value="'+ curInfo.lastName +'"></td><td><input type="text" value="'+ curInfo.email +'"></td><td><div onclick="removeRow(this)">-</div></td></tr>';
		};
		var arrTrs = $("#tblCustomer tr");
		for( var i = arrTrs.length - 1; i >= 1; i--){
			arrTrs.eq(i).remove();
		}
		$("#tblCustomer tr:last").after(strHtml);
		$("#customerModal").modal("show");
	});
}
function onCustomerSave(){
	var custInfoTrs = $("#tblCustomer tr");
	var arrCustInfos = [];
	for( var i = 1; i < custInfoTrs.length; i++){
		var curTr = custInfoTrs.eq(i);
		var firstName = curTr.find('input').eq(0).val();
		var lastName = curTr.find('input').eq(1).val();
		var email = curTr.find('input').eq(2).val();
		if( firstName == "" && lastName == "" && email == ""){
			continue;
		}
		var custInfo = {firstName: firstName, lastName: lastName, email: email};
		arrCustInfos.push( custInfo);
	}
	console.log(arrCustInfos);
	jQuery.post("library/clientEmails.php", {ClientDatas: JSON.stringify(arrCustInfos)}, function(data){
			$("#customerModal").modal("hide");
		});
}
function removeRow(_this){
	$(_this).parent().parent().remove();
}
function addRow(){
	var strHtml = '<tr><td><input type="text"></td><td><input type="text"></td><td><input type="text"></td><td><div onclick="removeRow(this)">-</div></td></tr>';
	$("#tblCustomer tr:last").after(strHtml);
}
function myCallback(pdf){
	console.log("adsf");
	console.log(pdf);
}
var pdfOutput;
function exportPDF(){
	var isEditOn = $(".edit_on").length;
	if( isEditOn){
		$(".edit_on").removeClass("edit_on").addClass("edit_off");
	}
	jQuery(".pdfHiden").hide();
	jQuery("table").css('border', 'none');
	jQuery("table td").css('border', 'none');
	jQuery("hr").hide();
	var source = window.document.getElementsByTagName("body")[0];
	var opt = {
		margin: 0.5,
		filename: jQuery("b").html()+ "_"+'en',
		jsPDF: { unit: "in", format: "letter", orientation: "portrait"},
		callback: function(pdf){
			console.log("asdfasdf");
			console.log(pdf);
		}
	};
	pdfOutput = html2pdf().from(source).set(opt).output();
	console.log(html2pdf( source, opt));
	// html2pdf().from(source).set(opt).save();
	setTimeout( function(){
		jQuery("table td").css('border', '1px solid gray');
		jQuery(".pdfHiden").show();
		jQuery("hr").show();
		if( isEditOn){
			$(".edit_off").removeClass("edit_off").addClass("edit_on");
		}
	}, 200);
}