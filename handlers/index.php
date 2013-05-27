<?php

$page->add_style (sprintf (
	'<link rel="alternate" type="application/rss+xml" href="http://%s/events/rss" />',
	$_SERVER['HTTP_HOST']
));

if (count ($this->params) > 0) {
	require_once ('apps/events/lib/Filters.php');

	$e = new Event ($this->params[0]);
	if ($e->error) {
		$page->title = __ ('Event not found');
		printf ('<p><a href="/events">&laquo; %s</a></p>', __ ('Back'));
		return;
	}

	$page->id = 'events';
	$page->title = $e->title;
	$page->layout = $appconf['Events']['event_layout'];
	$e->details = $tpl->run_includes ($e->details);
	echo $tpl->render ('events/event', $e->orig ());
} else {
	if (! $this->internal) {
		$page->id = 'events';
		$page->title = __ ($appconf['Events']['title']);
		$page->layout = $appconf['Events']['layout'];
	}
	$page->add_script ('/apps/events/js/fullcalendar/fullcalendar.min.js');
	$page->add_style ('/apps/events/js/fullcalendar/fullcalendar.css');
	if (strlen ($appconf['Events']['gcal_link']) > 0) {
		$page->add_script ('/apps/events/js/fullcalendar/gcal.js');
	}
	echo $tpl->render (
		'events/index',
		array (
			'gcal_link' => $appconf['Events']['gcal_link']
		)
	);
}

?>