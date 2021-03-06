<?php

/**
 * Sidebar list of events.
 */

$data['limit'] = (isset ($data['limit']) && is_numeric ($data['limit'])) ? $data['limit'] : 10;

if (! isset ($data['details']) || ($data['details'] !== 'no' && $data['details'] !== 'yes')) {
    $data['details'] = 'no';
}

$start = gmdate ('Y-m-d');

if (isset ($data['category']) && $data['category'] !== '') {
	$data['events'] = Event::query ()
		->where (function ($q) {
			$q->where ('access', 'public');
			if (User::require_login ()) {
				$q->or_where ('access', 'member');
			}
		})
		->where ('category', $data['category'])
	    ->where ('start_date >= "' . $start . '"')
	    ->order ('start_date', 'asc')
	    ->fetch_orig ($data['limit']);
} else {
	$data['events'] = Event::query ()
		->where (function ($q) {
			$q->where ('access', 'public');
			if (User::require_login ()) {
				$q->or_where ('access', 'member');
			}
		})
	    ->where ('start_date >= "' . $start . '"')
	    ->order ('start_date', 'asc')
	    ->fetch_orig ($data['limit']);
}

foreach ($data['events'] as $key => $event) {
    $data['events'][$key]->date = $event->start_date . ' ' . $event->starts;
}

if ($data['details'] === 'yes') {
	$this->run ('admin/util/minimal-grid');
	$page->add_style ('/apps/events/css/events.css');
    echo $tpl->render ('events/list', $data);
} else {
    echo $tpl->render ('events/sidebar', $data);
}
