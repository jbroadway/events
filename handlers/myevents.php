<?php

$this->require_login ();

$events = events\Registration::my_events ();

$this->run ('admin/util/minimal-grid');
$page->add_style ('/apps/events/css/events.css');

if (! $this->internal) {
	$page->title = __ ('My events');
}

echo View::render (
	'events/myevents',
	array (
		'events' => $events
	)
);
