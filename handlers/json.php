<?php

$page->layout = false;

$start = gmdate ('Y-m-d H:i:s', strtotime ($_GET['start']));
$end = gmdate ('Y-m-d H:i:s', strtotime ($_GET['end']));

$events = array ();

if (isset ($_GET['category'])) {
	$res = Event::query ()
		->where (function ($q) {
			$q->where ('access', 'public');
			if (User::require_login ()) {
				$q->or_where ('access', 'member');
			}
		})
		->where ('category', $_GET['category'])
	    ->where ('start_date >= ?', $start)
	    ->where ('end_date <= ?', $end)
	    ->fetch_orig ();
} else {
	$res = Event::query ()
		->where (function ($q) {
			$q->where ('access', 'public');
			if (User::require_login ()) {
				$q->or_where ('access', 'member');
			}
		})
	    ->where ('start_date >= ?', $start)
	    ->where ('end_date <= ?', $end)
	    ->fetch_orig ();
}

foreach ($res as $row) {
    $e = array (
        'title' => $row->title,
        'start' => $row->start_date . ' ' . $row->starts,
        'allDay' => false,
        'url' => '/events/' . $row->id . '/' . URLify::filter ($row->title),
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
