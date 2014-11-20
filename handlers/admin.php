<?php

$page->layout = 'admin';

$this->require_acl ('admin', 'events');

$limit = 20;
$_GET['offset'] = (isset ($_GET['offset'])) ? $_GET['offset'] : 0;

$lock = new Lock ();

$events = Event::query ('id, title, start_date, end_date, starts, ends, category, available')
    ->order ('start_date desc')
    ->fetch_orig ($limit, $_GET['offset']);
$count = Event::query ()->count ();
$ids = array ();

foreach ($events as $k => $e) {
    $ids[] = $e->id;
    $events[$k]->locked = $lock->exists ('Event', $e->id);
    if ($e->end_date === '' || $e->end_date === '0000-00-00' || $e->end_date === $e->start_date) {
        $e->end_date = false;
    }
}

$guests = Event::guests ($ids);
foreach ($events as $k => $e) {
    $events[$k]->guests = isset ($guests[$e->id]) ? $guests[$e->id] : 0;
}

$page->title = i18n_get ('Events');
echo $tpl->render ('events/admin', array (
    'events' => $events,
    'limit' => $limit,
    'total' => $count,
    'offset' => $_GET['offset'],
    'count' => count ($events),
    'url' => '/events/admin?offset=%d'
));
