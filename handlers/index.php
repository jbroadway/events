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

	$page->title = $e->title;
	$page->layout = $appconf['Events']['event_layout'];
	$e->details = $tpl->run_includes ($e->details);
	echo $tpl->render ('events/event', $e->orig ());
} else {
	if (! $this->internal) {
		$page->title = __ ($appconf['Events']['title']);
		$page->layout = $appconf['Events']['layout'];
	}
	$page->add_script ('<script src="/apps/events/js/fullcalendar/fullcalendar.min.js"></script>');
	$page->add_script ('<link rel="stylesheet" type="text/css" href="/apps/events/js/fullcalendar/fullcalendar.css" />');
	echo $tpl->render ('events/index');
}

?>