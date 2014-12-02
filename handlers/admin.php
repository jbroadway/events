<?php

$page->layout = 'admin';

$this->require_acl ('admin', 'events');

$limit = 20;
$_GET['offset'] = (isset ($_GET['offset'])) ? $_GET['offset'] : 0;
$c = isset ($_GET['c']) ? $_GET['c'] : '';
$q = isset ($_GET['q']) ? $_GET['q'] : '';
$q_fields = array ('title', 'details', 'venue', 'address', 'city', 'contact', 'email');
$q_exact = array ();
$url = ! empty ($q)
	? '/events/admin?q=' . urlencode ($q) . '&offset=%d'
	: '/events/admin?offset=%d';

$lock = new Lock ();

$events = Event::query ('id, title, start_date, end_date, starts, ends, category, available')
	->where_search ($q, $q_fields, $q_exact)
	->and_where (function ($q) use ($c) {
		if ($c !== '') {
			$q->where ('category', $c);
		} else {
			$q->where ('1 = 1');
		}
	})
    ->order ('start_date desc')
    ->fetch_orig ($limit, $_GET['offset']);

$count = Event::query ()
	->where_search ($q, $q_fields, $q_exact)
	->and_where (function ($q) use ($c) {
		if ($c !== '') {
			$q->where ('category', $c);
		} else {
			$q->where ('1 = 1');
		}
	})
	->count ();

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
	'url' => $url,
	'q' => $q,
	'c' => $c,
	'categories' => events\Category::query ()
		->order ('name', 'asc')
		->fetch_assoc ('id', 'name')
));
