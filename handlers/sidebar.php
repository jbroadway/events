<?php

/**
 * Sidebar list of events.
 */

require_once ('apps/events/lib/Filters.php');

$data['limit'] = (isset ($data['limit']) && is_numeric ($data['limit'])) ? $data['limit'] : 10;

if (! isset ($data['details']) || ($data['details'] !== 'no' && $data['details'] !== 'yes')) {
	$data['details'] = 'no';
}

$start = gmdate ('Y-m-d');

$data['events'] = Event::query ()
	->where ('start_date >= "' . $start . '"')
	->order ('start_date', 'asc')
	->fetch_orig ($data['limit']);

foreach ($data['events'] as $key => $event) {
	$data['events'][$key]->date = $event->start_date . ' ' . $event->starts;
}

if ($data['details'] === 'yes') {
	echo $tpl->render ('events/list', $data);
} else {
	echo $tpl->render ('events/sidebar', $data);
}

?>