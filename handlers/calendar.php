<?php

$page->add_style (sprintf (
	'<link rel="alternate" type="application/rss+xml" href="http://%s/events/rss" />',
	Appconf::admin ('Site Settings', 'site_domain')
));

if (! $this->internal) {
	$page->id = 'events';
	$page->title = __ ($appconf['Events']['title']);
	$page->layout = $appconf['Events']['layout'];

	if (isset ($this->params[0]) && $this->params[0] === 'category') {
		if (! isset ($this->params[1])) {
			$this->redirect ('/events');
		}
		$category = $this->params[1];
	} else {
		$category = false;
	}
} else {
	$category = isset ($data['category']) ? $data['category'] : false;
}

$this->run ('admin/util/minimal-grid');
$page->add_script ('/apps/events/css/events.css');
$page->add_script ('/apps/events/js/fullcalendar/lib/moment.min.js');
$page->add_script ('/apps/events/js/fullcalendar/fullcalendar.min.js');
$page->add_style ('/apps/events/js/fullcalendar/fullcalendar.css');
if (strlen ($appconf['Events']['gcal_browser_key']) > 0) {
	$page->add_script ('/apps/events/js/fullcalendar/gcal.js');
}
if (file_exists ('apps/events/js/fullcalendar/lang/' . $i18n->language . '.js')) {
	$page->add_script ('/apps/events/js/fullcalendar/lang/' . $i18n->language . '.js');
}
echo $tpl->render (
	'events/calendar',
	array (
		'gcal_id' => $appconf['Events']['gcal_id'],
		'gcal_browser_key' => $appconf['Events']['gcal_browser_key'],
		'category' => $category,
		'categories' => events\Category::query ()->order ('name', 'asc')->fetch_assoc ('id', 'name')
	)
);

?>