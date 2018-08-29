<style type="text/css">
	div.main_container{width: 100%/*980px*/; margin: 0px auto;}
	div.menu_bar{width: 100px; display: inline-block; border: solid 1px gray; background-color: #ffffff; color: black; border-top-left-radius: 10px; border-top-right-radius: 10px; text-align: center; font-size: 18px; height: 24px; line-height: 24px; cursor: pointer;}
	div.menu_bar.current{background-color: mediumblue; color: white;}
	table{width: 100%;}
	table, td{border: solid 1px gray; border-collapse: collapse;}
	td{padding: 5px; line-height: 20px;}
	tr.header{ background-color: green; color: white; text-align: center;}
	input.txt_data, input.pos_data, input.jpn_data{ background: transparent; width: 100%; border: 0; line-height: 24px; height: 24px; font-size: 16px;}
	tr.google_data input.txt_data{
		background-image: url(assets/imgs/google.png);
		background-size: contain;
    	background-repeat: no-repeat;
    	text-indent: 30px;
	}
	tr.hand_data input.txt_data{
		background-image: url(assets/imgs/hand.png);
		background-size: contain;
    	background-repeat: no-repeat;
    	text-indent: 30px;
	}
	span.gap {
		display: inline-block;
		width: 100px; height: 30px;
	}
	.page_num, .cur_page {
		display: inline-block;
		text-align: center;
		width: 30px;
		height: 30px;
		line-height: 30px;
		border: solid 1px gray;
		border-radius: 5px;
		margin: 3px;
	}
	.cur_page {
		background-color: #cccccc;
	}

	.page_num:hover {
		cursor: pointer;
		background-color: green;
		color: white;
	}

	.hand_data td{
		background-color: #f1fdf1;
	}

	.hand_data td input {
		color: #004e00;
	}
</style>
<script language="javascript">
	function show_loading_message() {
	    if(jQuery('.loading_message').length == 0)
	        jQuery("body").append('<div class="loading_message" style="position:fixed; left:0; top: 0; background-color:black;opacity:0.3;height:calc(100%); width:calc(100%);z-index:99999999;"></div><div class="loading_message" style="position:fixed; left:0; top: 0; height:calc(100%); width:calc(100%);z-index:199999999;"><div style="margin:auto; margin-top:calc(40vh); width:100px; height:100px; background-color: white; border-radius: 10px; background-image: url(assets/imgs/loading.gif); background-size: 150% 150%; background-position: center center;"></div></div>');
	    jQuery('.loading_message').show();
	}

	function hide_loading_message() {
	    jQuery('.loading_message').hide();
	}
</script>

<div class="main_container" style="padding: 10px;">
	<div class="menu_bar current">Words</div><div class="menu_bar">Sentences</div>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<select id="check_case" onchange="load_data();">
		<option value="google">Google-Translated</option>
		<option value="hand">Hand-Written</option>
		<option value="">All</option>
	</select>
	&nbsp;
	<input type=text id="search_key" name="search_key" placeholder="Enter search keyword">
	&nbsp;
	<select id="sort_field" onchange="load_data();">
		<option value="jpn asc">Japanese ASC</option>
		<option value="jpn desc">Japanese DESC</option>
		<option value="Id asc">ID number ASC</option>
		<option value="Id desc">ID number DESC</option>
		<option value="freq asc">Frequency ASC</option>
		<option value="freq desc">Frequency DESC</option>
	</select>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type=button id="cmdExport" value="Export">
	&nbsp;&nbsp;
	<input type=button id="cmdImport" value="Import">
	&nbsp;&nbsp;
	<input type=button id="cmdEdit" value="Edit all on this page">
	&nbsp;&nbsp;
	<input type=button id="cmdNew" value="Add New">
	<input type=file id="import_file" style="display: none;" accept=".xls">
	<table id="tbl_words">
		<thead>
			<tr class="header"><td style="width:50px;">No</td><td>Japanese</td><td>English</td><td style="width: 140px;">Action</td></tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
	<table id="tbl_sentences" style="display: none;">
		<thead>
			<tr class="header"><td rowspan=2 style="width:40px;">No</td><td colspan="2" style="width: 720px">Japanese</td><td rowspan="2" style="width:60px;">Frequency</td><td rowspan="2" style="width:60px;">Report Links</td><td rowspan="2" style="width: 140px;">Action</td></tr>
			<tr class="header"><td>English</td><td style="width:70px;">Position</td></tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
	<div id="page_word" style="margin-top: 5px;"></div>
	<div id="page_sentences" style="margin-top: 5px; display: none;"></div>


</div>

<div class="modal fade" id="newWordModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				<button id="btn_new_word_save" type="button" class="btn btn-primary" onclick="onNewWordSave()">Save</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="newSentenceModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="Id" style="color: black;">Add New Sentence</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
					<div class="form-group">
		                <label for="source" class="col-form-label" style="color: gray;">Sentence (source)</label>
						<textarea class="form-control" name="source" id="source" rows="4" required=""></textarea>
					</div>
					<div class="form-group">
						<label for="destination" class="col-form-label" style="color: gray;">Sentence (target)</label>
						<textarea class="form-control" name="destination" id="destination" rows="3"></textarea>
					</div>
					<div class="form-group">
						<label for="pattern" class="col-form-label" style="color: gray;">Pattern Rule</label>
						<input type="text" name="pattern" class="form-control" id="pattern">
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button id="btn_customer_save" type="button" class="btn btn-primary" onclick="onNewSentenceSave()">Save</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$ = jQuery;
	var remove_data;
	function reload_data(select_cases) {
		$("#tbl_words input").attr("readonly", true);
		$("#tbl_sentences input").attr("readonly", true);
		if(select_cases == "google") {
			$("tr.hand_data").hide();
			$("tr.google_data").show();
		} else if(select_cases == "hand") {
			$("tr.google_data").hide();
			$("tr.hand_data").show();
		} else {
			$("tr.google_data").show();
			$("tr.hand_data").show();
		}
		$(".btn_edit").unbind("click").click(function(){
			var idx = $(this).parent().parent().index();
			var item = $(this).parent().parent().parent().find("tr").eq(idx).find("input.txt_data");
			if( item.length != 0){
				item.attr("readonly", false);
				$(this).parent().parent().parent().find("tr").eq(idx).find("td").eq(2).css("background-color", "lightcyan");
			} else{
				// $(this).parent().parent().parent().find("tr").eq(idx).find("td").css("background-color", "lightcyan");
				$(this).parent().parent().parent().find("tr").eq(idx * 1 + 1).find("td").css("background-color", "lightcyan");
				$(this).parent().parent().parent().find("tr").eq(idx * 1 + 1).find("input.txt_data").attr("readonly", false);
			}
		});
		$(".btn_remove").unbind("click").click(function(){
			if(confirm("Are you sure to remove that pattern?")){
				remove_data = $(this).parent().parent().attr("aria");
				$.post("library/trans_api.php", {"db_name": "<?= $db_name;?>", "case": 3, "data": $(this).parent().parent().attr("aria")}, function(data){
					$("tr." + remove_data).remove();
				});
			}
		});
		$(".btn_link").unbind("click").click(function(){
			var data = $(this).parent().parent().attr("aria");
			window.open("phrase_link.php?data=" + data);
		});
		$(".btn_save").unbind("click").click(function(__param){
			jpn_data = $("tr." + $(this).parent().parent().attr("aria") + " input.jpn_data").eq(0).prop("value");
			eng_data = $("tr." + $(this).parent().parent().attr("aria") + " input.txt_data").eq(0).prop("value");
			pos_data = "";
			if($("tr." + $(this).parent().parent().attr("aria") + " input.pos_data").length > 0)
				pos_data = $("tr." + $(this).parent().parent().attr("aria") + " input.pos_data").eq(0).prop("value");
			check_hand = 0;
			if(typeof __param.pageX != "undefined") {
				check_hand = 1;
			}
			if(check_hand) show_loading_message();
			$.post("library/trans_api.php", {"db_name": "<?= $db_name;?>", "case": 4, "data": $(this).parent().parent().attr("aria"), "jpn_data": jpn_data, "eng_data": eng_data, "pos_data": pos_data, "check_hand": check_hand}, function(data){
				if(data){
					$("."+data).removeClass("google_data").addClass("hand_data");
					$("."+data).find("td").css("background-color", "#f1fdf1");
				}
				hide_loading_message();
			});
		});
		
		$("#tbl_sentences input[type=text]").unbind("keydown").keydown(function(e){
			$(this).addClass("changed");
		});

		$("#tbl_sentences input[type=text]").unbind("blur").blur(function(){
			if($(this).hasClass("changed")) {
				$(this).removeClass("changed");
				$("tr." + $(this).parent().parent().attr("aria") + " .btn_save").click();
			}
		});
		
	}

	function load_words(__page_num) {
		show_loading_message();
		$.post("library/trans_api.php", {"db_name": "<?= $db_name;?>", "case": 1, page_num: __page_num, select_case: $("#check_case option:selected").prop("value"), search_key: $("#search_key").prop("value"), sort_field:$("#sort_field").prop("value")}, function(data){
			hide_loading_message();
			$("#tbl_words tbody").html(data);
			reload_data($("#check_case option:selected").prop("value"));
		});
		$.post("library/trans_api.php", {"db_name": "<?= $db_name;?>", "case": 11, page_num: __page_num, select_case: $("#check_case option:selected").prop("value"), search_key: $("#search_key").prop("value"), sort_field:$("#sort_field").prop("value")}, function(data){
			$("#page_word").html(data);
			$("#goto_page_word").unbind("keydown").keydown(function(e){
				if(e.keyCode == 13) {
					page_check_val = parseInt($(this).prop("value"));
					if(isNaN(page_check_val)) $(this).prop("value", 1);
					$(this).prop("value", page_check_val);
					load_words(parseInt($(this).prop("value")) - 1);
				}
			});
		});
	}

	function load_sentences(__page_num) {
		show_loading_message();
		$.post("library/trans_api.php", {"db_name": "<?= $db_name;?>", "case": 2, page_num: __page_num, select_case: $("#check_case option:selected").prop("value"), search_key: $("#search_key").prop("value"), sort_field:$("#sort_field").prop("value")}, function(data){
			hide_loading_message();
			$("#tbl_sentences tbody").html(data);
			reload_data($("#check_case option:selected").prop("value"));
		});
		$.post("library/trans_api.php", {"db_name": "<?= $db_name;?>", "case": 21, page_num: __page_num, select_case: $("#check_case option:selected").prop("value"), search_key: $("#search_key").prop("value"), sort_field:$("#sort_field").prop("value")}, function(data){
			$("#page_sentences").html(data);
			$("#goto_page_sentence").unbind("keydown").keydown(function(e){
				if(e.keyCode == 13) {
					page_check_val = parseInt($(this).prop("value"));
					if(isNaN(page_check_val)) $(this).prop("value", 1);
					$(this).prop("value", page_check_val);
					load_sentences(parseInt($(this).prop("value")) - 1);
				}
			});
		});
	}

	function load_data() {
		load_words(0);
		load_sentences(0);
	}

	$(".menu_bar").click(function(){
		$(".menu_bar").removeClass("current");
		$(this).addClass("current")
		if($(".menu_bar").index($(this)) == 0){
			$("#tbl_sentences").hide();
			$("#page_sentences").hide();
			$("#tbl_words").show();
			$("#page_word").show();
		} else {			
			$("#tbl_words").hide();
			$("#page_word").hide();
			$("#tbl_sentences").show();
			$("#page_sentences").show();
		}
	});

	$("#cmdExport").click(function(){
		export_url = "export.php?case="+$("#check_case").prop("value")+"&type="+$(".menu_bar.current").html();
		window.open(export_url);
	});

	$("#cmdNew").click(function(){
		if( $("#tbl_words").is(":visible")){
			$("#newWordModal .form-control").val("");
			$("#newWordModal").modal("show");
		} else{
			$("#newSentenceModal .form-control").val("");
			$("#newSentenceModal").modal("show");
		}
	});
	function onNewWordSave(){
		var srcWord = $("#newWordModal .form-control[name='source']").val();
		var dstWord = $("#newWordModal .form-control[name='destination']").val();
		if( srcWord == "" || dstWord ==""){
			alert("Source and Destination word can't be empty.");
			return;
		}
		$.post("library/trans_api.php", {"db_name": "<?= $db_name;?>", "case": 2001, src: srcWord, dst: dstWord}, function(data){
			$("#newWordModal").modal("hide");
		});
	} 
	function onNewSentenceSave(){
		var srcWord = $("#newSentenceModal .form-control[name='source']").val();
		var dstWord = $("#newSentenceModal .form-control[name='destination']").val();
		var pattern = $("#newSentenceModal .form-control[name='pattern']").val();
		if( srcWord == "" || dstWord ==""){
			alert("Source and Destination word can't be empty.");
			return;
		}
		$.post("library/trans_api.php", {"db_name": "<?= $db_name;?>", "case": 2002, src: srcWord, dst: dstWord, pattern: pattern}, function(data){
			$("#newSentenceModal").modal("hide");
		});
	}

	$("#cmdEdit").click(function(){
		if( $("#tbl_words").is(":visible") ){
			$("#tbl_words input.txt_data").attr("readonly", false);
			var trs = $("#tbl_words tbody tr");
			for (var i = trs.length - 1; i >= 0; i--) {
				var tr = trs.eq(i);
				tr.find("td").eq(2).css("background-color", "lightcyan");
			}
		} else{
			$("#tbl_sentences input.txt_data").attr("readonly", false);
			var trs = $("#tbl_sentences tbody tr");
			for( var i = 1; i < trs.length; i+= 2){
				var tr = trs.eq(i);
				tr.find("td").eq(0).css("background-color", "lightcyan");
			}
		}
	});
	function transferExcelComplete(e) {
		alert("Excel Import Success ... ");
		load_data();
	}

	$("#import_file").change(function(event){
        var file = event.target.files[0];
        var data = new FormData();

        data.append("uploadedFile", file);
        var objXhr = new XMLHttpRequest();
        objXhr.addEventListener("load", transferExcelComplete, false);

        objXhr.open("POST", "library/import.php?case="+$("#check_case").prop("value")+"&type="+$(".menu_bar.current").html());
        objXhr.send(data);
    })

	$("#cmdImport").click(function(){
		$("#import_file").click();
	});

	$("#search_key").keydown(function(e){
		if(e.keyCode == 13) {
			load_data();
		}
	});

	$(function(){
		load_data();
	});
</script>