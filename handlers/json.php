<?php

require_once ('apps/events/lib/Filters.php');

$page->template = false;

$start = gmdate ('Y-m-d H:i:s', $_GET['start']);
$end = gmdate ('Y-m-d H:i:s', $_GET['end']);

$events = array ();
$res = Event::query ()
	->where ('start_date >= "' . $start . '"')
	->where ('end_date <= "' . $end . '"')
	->fetch_orig ();

foreach ($res as $row) {
	$e = array (
		'title' => $row->title,
		'start' => $row->start_date . ' ' . $row->starts,
		'allDay' => false,
		'url' => '/events/' . $row->id . '/' . events_filter_title ($row->title),
		'backgroundColor' => '#def',
		'borderColor' => '#cde',
		'textColor' => '#000'
	);
	if (! empty ($row->end_date)) {
		$e['end'] = $row->end_date . ' ' . $row->ends;
	}
	$events[] = $e;
}

header ('Content-Type: application/json');
echo json_encode ($events);
exit;

?>