<?php
	if (!defined('INCLUDE_SECURE')){
		hack();
	}

	/*
	 * Generates permalink for a particular cycle for a prof
	 */
	function generate_impression_link($cycle_id, $pid, $spoof = false) {
		return makelink('impressions','permalink',permalink_encode($cycle_id, $pid) . ($spoof ? '/spoof' : ''));
	}

	function permalink_encode($cycle_id, $pid) {
		return encode(json_encode( array($cycle_id, $pid)));
	}

	function permalink_decode($code) {
		return json_decode(decode($code));
	}
?>