<?php

$page->add_style (sprintf (
    '<link rel="alternate" type="application/rss+xml" href="http://%s/events/rss" />',
    Appconf::admin ('Site Settings', 'site_domain')
));

if (! $this->internal) {
	$page->id = 'events';
	$page->title = __ ('Past Events');
	$page->layout = $appconf['Events']['layout'];
}

$this->run ('admin/util/minimal-grid');
$page->add_style ('/apps/events/css/events.css');

if (isset ($this->params[0]) && $this->params[0] === 'category') {
	if (! isset ($this->params[1])) {
		$this->redirect ('/events/past');
	}
	$category = $this->params[1];
} else {
	$category = false;
}

$data = array (
	'limit' => 20,
	'details' => 'yes',
	'events' => array (),
	'category' => $category,
	'categories' => events\Category::query ()->order ('name', 'asc')->fetch_assoc ('id', 'name')
);

$start = gmdate ('Y-m-d');

if ($category) {
	$data['events'] = Event::query ()
		->where (function ($q) {
			$q->where ('access', 'public');
			if (User::require_login ()) {
				$q->or_where ('access', 'member');
			}
		})
		->where ('category', $category)
		->where ('end_date < "' . $start . '"')
		->order ('start_date', 'desc')
		->fetch_orig ($data['limit']);
} else {
	$data['events'] = Event::query ()
		->where (function ($q) {
			$q->where ('access', 'public');
			if (User::require_login ()) {
				$q->or_where ('access', 'member');
			}
		})
		->where ('end_date < "' . $start . '"')
		->order ('start_date', 'desc')
		->fetch_orig ($data['limit']);
}

$data['events'] = is_array ($data['events']) ? $data['events'] : array ();

foreach ($data['events'] as $key => $event) {
	$data['events'][$key]->date = $event->start_date . ' ' . $event->starts;
}

echo $tpl->render ('events/past', $data);
