<?php
	
	require "trans_lib.php";

	$sentences = [];
	$log_dir = glob("../logs/backup/*.event");
	foreach ($log_dir as $log_file) {
		$data = @file_get_contents($log_file);
		if($data) {
			$data_json = json_decode($data);
			if($data_json) {
				foreach ($data_json as $event_json) {
					$event = $event_json->event;
					if(count($event) == 0) $sentences[] = "Nothing to report.";
					foreach ($event as $value) {
						$sentences[] = $value;
					}
				}
			}
		}
	};

	calculate_frequency($sentences);

?>