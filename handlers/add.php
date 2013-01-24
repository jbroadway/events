<?php

$page->layout = 'admin';

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

$f = new Form ('post', 'events/add');
$f->verify_csrf = false;
if ($f->submit ()) {
	$e = new Event ($_POST);
	$e->put ();
	Versions::add ($e);
	if (! $e->error) {
		$this->add_notification ('Event added.');

		require_once ('apps/events/lib/Filters.php');
		$_POST['page'] = 'events/' . $e->id . '/' . events_filter_title ($_POST['title']);
		$_POST['body'] = $_POST['details'];
		$this->hook ('events/add', $_POST);

		if (isset ($_GET['return'])) {
			$this->redirect ($_GET['return']);
		}
		$this->redirect ('/events/admin');
	}
	$page->title = 'An Error Occurred';
	echo 'Error Message: ' . $e->error;
} else {
	$e = new Event;
	$e->start_date = gmdate ('Y-m-d');
	$e->starts = '18:00:00';
	$e->ends = '20:00:00';

	$e->failed = $f->failed;
	$e = $f->merge_values ($e);
	$page->title = 'Add Event';
	$this->run ('admin/util/wysiwyg');
	echo $tpl->render ('events/add/head', $e);
	echo $tpl->render ('events/add', $e);
}

?>