<?php

$page->layout = 'admin';

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

require_once ('apps/events/lib/Filters.php');

$limit = 20;
$_GET['offset'] = (isset ($_GET['offset'])) ? $_GET['offset'] : 0;

$lock = new Lock ();

$events = Event::query ('id, title, start_date, end_date, starts, ends')
	->order ('id asc')
	->fetch_orig ($limit, $_GET['offset']);
$count = Event::query ()->count ();

foreach ($events as $k => $e) {
	$events[$k]->locked = $lock->exists ('Event', $e->id);
}

$page->title = i18n_get ('Events');
echo $tpl->render ('events/admin', array (
	'events' => $events,
	'count' => $count,
	'offset' => $_GET['offset'],
	'more' => ($count > $_GET['offset'] + $limit) ? true : false,
	'prev' => $_GET['offset'] - $limit,
	'next' => $_GET['offset'] + $limit
));

?>