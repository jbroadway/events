<?php

$page->add_style (sprintf (
	'<link rel="alternate" type="application/rss+xml" href="http://%s/events/rss" />',
	$_SERVER['HTTP_HOST']
));

if (count ($this->params) > 0) {
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
	$e->remaining = $e->available ();
	if ($e->end_date === '' || $e->end_date === '0000-00-00' || $e->end_date === $e->start_date) {
		$e->end_date = false;
	}
	$e->has_passed = ($e->start_date . ' ' . $e->starts) < gmdate ('Y-m-d H:i:s');
	echo $tpl->render ('events/event', $e->orig ());
} else {
	if (! $this->internal) {
		$page->id = 'events';
		$page->title = __ ($appconf['Events']['title']);
		$page->layout = $appconf['Events']['layout'];
	}
	$page->add_script ('/apps/events/js/fullcalendar/lib/moment.min.js');
	$page->add_script ('/apps/events/js/fullcalendar/fullcalendar.min.js');
	$page->add_style ('/apps/events/js/fullcalendar/fullcalendar.css');
	if (strlen ($appconf['Events']['gcal_link']) > 0) {
		$page->add_script ('/apps/events/js/fullcalendar/gcal.js');
	}
	if (file_exists ('apps/events/js/fullcalendar/lang/' . $i18n->language . '.js')) {
		$page->add_script ('/apps/events/js/fullcalendar/lang/' . $i18n->language . '.js');
	}
	echo $tpl->render (
		'events/index',
		array (
			'gcal_link' => $appconf['Events']['gcal_link']
		)
	);
}

?>