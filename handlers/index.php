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
	
	$data = array (
		'limit' => 20,
		'details' => 'yes',
		'events' => array ()
	);

	$start = gmdate ('Y-m-d');

	$data['events'] = Event::query ()
		->where ('start_date >= "' . $start . '"')
		->order ('start_date', 'asc')
		->fetch_orig ($data['limit']);

	foreach ($data['events'] as $key => $event) {
		$data['events'][$key]->date = $event->start_date . ' ' . $event->starts;
	}
	
	echo $tpl->render ('events/index', $data);
}
