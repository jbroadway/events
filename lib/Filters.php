<?php

function events_filter_date ($d) {
	return date ('l, F jS', strtotime ($d));
}

function events_filter_time ($t) {
	list ($h, $m, $s) = explode (':', $t);
	if ($h > 12) {
		return ($h - 12) . ':' . $m . 'pm';
	}
	return (int) $h . ':' . $m . 'am';
}

function events_filter_title ($t) {
	return trim (preg_replace ('/[^a-z0-9-]+/', '-', strtolower ($t)), '-');
}

?>