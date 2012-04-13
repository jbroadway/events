<?php

/**
 * Sidebar list of events.
 */

require_once ('apps/events/lib/Filters.php');

$data['limit'] = (isset ($data['limit']) && is_numeric ($data['limit'])) ? $data['limit'] : 10;

if (! isset ($data['details']) || ($data['details'] !== 'no' && $data['details'] !== 'yes')) {
	$data['details'] = 'no';
}

$start = gmdate ('Y-m-d 00:00:00');

$data['events'] = Event::query ()
	->where ('start_date >= "' . $start . '"')
	->fetch_orig ($data['limit']);

if ($data['details'] === 'yes') {
	echo $tpl->render ('events/list', $data);
} else {
	echo $tpl->render ('events/sidebar', $data);
}

?>