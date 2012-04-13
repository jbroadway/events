<?php

$page->layout = 'admin';

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

$lock = new Lock ('Event', $_GET['id']);
if ($lock->exists ()) {
	$page->title = i18n_get ('Editing Locked');
	echo $tpl->render ('admin/locked', $lock->info ());
	return;
}

$e = new Event ($_GET['id']);

// for hooks
require_once ('apps/events/lib/Filters.php');
$_GET['page'] = 'events/' . $e->id . '/' . events_filter_title ($e->title);

if (! $e->remove ()) {
	$page->title = 'An Error Occurred';
	echo 'Error Message: ' . $e->error;
	return;
}

$this->hook ('events/delete', $_GET);

$this->add_notification ('Event deleted.');
if (! isset ($_GET['return'])) {
	$this->redirect ('/events/admin');
} else {
	$this->redirect ($_GET['return']);
}

?>