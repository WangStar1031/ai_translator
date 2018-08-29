<?php
	require "library/jra_00.php";
	require_once 'library/trans_lib.php';

	require_once 'library/fpdf/fpdf.php';

	$font = "Helvetica";
	class CustomPDF extends FPDF{
		function Footer(){
			$font = "Helvetica";
			$this->SetY(-17);
			$this->SetFont($font, 'I', 8);
			$this->MultiCell(0,4, "Copyright Vision Translation Inc. - The information contained in this translation is non-transferrable and not for redistribution.\nFor other parties interested in this information please contact c.green@translate.vision.");
		}
		function Header(){
			$this->Image("assets/imgs/vision-logo.png", 10, 10, 8);
		}
	}

	function __get_raw_text($_txt){
		$retVal = "";
		$isStart = false;
		while(($startPos = strpos($_txt, "<")) !== false){
			$retVal .= substr($_txt, 0, $startPos);
			$_txt = substr($_txt, $startPos);
			$endPos = strpos($_txt, ">");
			$_txt = substr($_txt, $endPos+1);
		}
		$retVal .= $_txt;
		$retVal = str_replace("  ", " ", $retVal);
		return $retVal;
	}
	$pdf = new CustomPDF('P', 'mm', 'A4');

	$pdf->SetMargins(20, 20, 20);
	$pdf->SetFont($font, 'b', 12);
	$pdf->SetTextColor(80,80,80);

	$pdf->AddPage();

	$patterns = get_word_patterns();
	$sentences = get_sentence_patterns();

	$meetings = [];
	$schedules =  __get_race_schedule($date_str);
	foreach ($schedules as $meeting) {
		$meetings[$meeting->meeting_id] = $meeting->meeting_name;
	}
	$dic_data = json_decode(@file_get_contents("logs/backup/competitors_".$date_str."_dic.json"));

	$lang = "jp";
	if(isset($_GET['lang'])) $lang = $_GET['lang'];

	$arr = __get_extra_events(true, $date_str);

	echo '<style>table, td{border-collapse: collapse; border: solid 1px gray; padding: 4px;}</style>';
	$date_value = substr($date_str,0,4)."-".substr($date_str,4,2)."-".substr($date_str,6,2);
	$htmlBuff = '';
	if($lang == "jp"){
		$htmlBuff = "<b>".date("Y年n月j日", strtotime($date_value))."JRAスチュワードレポート</b>";
	} else{
		$htmlBuff = "<b>".date("F jS, Y", strtotime($date_value))." JRA Stewards Reports</b>";
	}
	echo $htmlBuff;
	$pdfName = date("F jS, Y", strtotime($date_value))." JRA Stewards Reports";

	$pdf->Cell(0,20,'JRA Stewards Reports',0,1,'C');

	$link_href = $_SERVER["REQUEST_URI"];
	if($lang == "en") $link_href = str_replace("&lang=en", "", $link_href);
	else $link_href .= "&lang=en";

	echo ' <a href="'.$link_href.'" style="text-decoration: none; color: green;"> ( '.(($lang == "en")?"Japanese":"英語").' ) </a>';
	if($lang == "en") {
		echo '&nbsp;&nbsp;&nbsp;<select class="pdfHiden" size=1 onchange="toggle_patterns(this.value);"><option value="edit_off">View</option><option value="edit_on">Edit</option></select>';
		echo "<div style='float:right;'>";
		echo "<a class='btnForExport pdfHiden' href='jrastewards/".$pdfName."_".$lang.".pdf' download>Export PDF</a>";
		echo "<div class='btnForExport pdfHiden' onclick='addCustomerEmail()'>Add Customer Email</div>";
		echo "<div class='btnForExport pdfHiden' onclick='sendEmails()'>Email Reports Now</div>";
		echo "<div style='clear:both;'></div>";
		echo "</div>";
	}
	echo "<br>";
	
	if($lang == "en") {
		echo '
<script src="assets/js/jquery.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<link href="assets/css/jra_steward_trans.css" rel="stylesheet">
		';
	}

	$meeting_id = 0;
	foreach ($arr as $event) {
		$event_string = trim(implode("", $event->event));
		if(!($event_string)){
			if($lang == "jp")
				$event->event = ["報告するものは何もありません。"];
			else
				$event->event = ["Nothing to report."];
		}
		$event_string = trim(implode("", $event->event));
		if($event_string){
			if(isset($event->meeting_name)) $meeting_name = $event->meeting_name;
			else $meeting_name = $meetings[$event->meeting_id];
			$meeting_name_printing = array();
			if($lang == "en") {
				$origin_event = $event->event;
				$pattern_matchings = [];
				$event->event = process_event($event->event);
				$event->event = str_replace(". (obstructing", " (obstructing", $event->event);
				$meeting_name_printing = process_meeting_for_printing($meeting_name);
				$meeting_name = process_meeting($meeting_name);
				foreach ($origin_event as $event_sentence) {
					$detail_pattern = [];
					$detail_event = explode("。", $event_sentence);
					for($i=0; $i<count($detail_event); $i++) {
						if(trim($detail_event[$i]))
							$detail_pattern[] = search_pattern_id($detail_event[$i], $sentences, process_individual_sentence($detail_event[$i]));
					}
					$pattern_matchings[] = $detail_pattern;
				}
				$dest_events = $event->event;
				for($i=0; $i<count($dest_events); $i++) {
					$event_sentence = $origin_event[$i];
					$detail_event = explode("。", $event_sentence);
					$dest_events[$i] = post_pattern_process($dest_events[$i], $pattern_matchings[$i], $detail_event);
				}
				$event->event = $dest_events;
			} else{
				$origin_event = $event->event;
				$dest_events = $event->event;
				for($i=0; $i<count($dest_events); $i++) {
					$event_sentence = $origin_event[$i];
					$dest_events[$i] = $event_sentence;
					// $dest_events[$i] = processJpnNames($event_sentence);
				}
				$event->event = $dest_events;
			}
			if($meeting_id !== $event->meeting_id) {
				if($meeting_id){
					echo '</table>';
					echo '<div class="html2pdf__page-break"></div>';

					$pdf->AddPage();
					$pdf->SetFont($font, 'b', 12);
					$pdf->SetTextColor(80,80,80);
					$pdf->Cell(0,20,'JRA Stewards Reports',0,1,'C');
				}
				$meeting_id = $event->meeting_id;
				echo '<br><h2><font color=blue>'.$meeting_name.'</font>';
				$arr4Day = array();
				$arr4Day = explode("Meeting", $meeting_name);

				if( $lang == "en"){
					$pdf->SetFont($font, 'b', 10);
					$pdf->SetTextColor(80,80,80);
					$pdf->Write( 6, $meeting_name_printing[0] . " - " . date("F jS, Y", strtotime($date_value)) . " - " . $meeting_name_printing[1] .  " Meeting " . trim($arr4Day[1]));
					$pdf->SetFont($font, 'b', 10);
					$pdf->Write( 6, ' ( '.$event->meeting_id .' )' . "\n");
					$pdf->Write(6, "\n");
					$pdf->Line(20, $pdf->GetY(), $pdf->GetPageWidth()-20, $pdf->GetY());
					$pdf->SetY( $pdf->GetY() + 1);			
				}
				echo ' ( '.$event->meeting_id.' )';
				
				echo '</h2><table>';
			}

			echo '<tr><td style="width:80px; text-align: center;">'.(($lang == "en")?"Race":"レース").' '.$event->event_number.'</td><td>'.implode('<hr>', $event->event).'</td></tr>';


			$pdf->SetFont($font, '', 10);
			$pdf->SetTextColor(100,100,100);
			$raceTxt = (($lang == "en")?"Race":"レース").' '.(($event->event_number > 9)?$event->event_number:' '.$event->event_number).' - ';
			$left = $pdf->GetStringWidth("Race 88 - ");
			$pdf->Write(6, $raceTxt);
			for( $idx = 0; $idx < count($event->event); $idx++){
				$strBuf = $event->event[$idx];
				$clean = str_replace(chr(194),"",__get_raw_text($strBuf));
				// $pdf->SetY($pdf->GetY());
				$pdf->SetX(20 + $left);
				$pdf->MultiCell(0, 6, trim(__get_raw_text($clean)) . "\n");
			}
			$pdf->SetY( $pdf->GetY() + 1);
			$pdf->Line(20, $pdf->GetY(), $pdf->GetPageWidth()-20, $pdf->GetY());
			$pdf->SetX(20);
			$pdf->SetY( $pdf->GetY() + 1);
		}
	}
	echo '</table>';
	// $pdf->WriteHTML('</table>');

	$pdf->Output( 'F', './jrastewards/' . $pdfName . '_' . $lang .'.pdf');

	if($lang == "en") {
		$htmlBuff = '
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="Id" style="color: black;">Edit Sentence Pattern</h5>
            <input type="hidden" name="Id" id="Id" value="0">
            <input type="hidden" name="trans_source" id="trans_source" value="">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
                          <div class="form-group">
                <label for="source" class="col-form-label" style="color: gray;">Sentence (source)</label>
                                <textarea class="form-control" name="source" id="source" placeholder="Example: 報告するものは何もありません。" rows="4" required=""></textarea>
                            </div>
                          <div class="form-group">
                <label for="destination" class="col-form-label" style="color: gray;">Sentence (target)</label>
                                <textarea class="form-control" name="destination" id="destination" placeholder="Example: Nothing to report." rows="3"></textarea>
                            </div>
                          <div class="form-group">
                <label for="pos" class="col-form-label" style="color: gray;">Pattern Rule</label>
                                <input type="text" class="form-control" name="pos" id="pos" placeholder="Example: 1">
                            </div>
                      </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button id="btn_save" type="button" class="btn btn-primary">Save</button>
          </div>
        </div>
      </div>
    </div>
		';
		echo $htmlBuff;
	$htmlBuff = '
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="Id" style="color: black;">Edit Customer Information</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <table id="tblCustomer">
                <tr>
                	<th>First Name</th>
                	<th>Last Name</th>
                	<th>Email</th>
                	<th></th>
                </tr>
              </table>
           	  <div class="btn-success btn" onclick="addRow()" style="margin-top: 10px; padding: 0px 5px;">Add</div>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button id="btn_customer_save" type="button" class="btn btn-primary" onclick="onCustomerSave()">Save</button>
          </div>
        </div>
      </div>
    </div>
		';
		echo $htmlBuff;
	}
?>
<script src="assets/js/jra_steward_trans.js"></script>
