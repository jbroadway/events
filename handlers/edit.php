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
} else {
	$lock->add ();
}

$e = new Event ($_GET['id']);

$f = new Form ('post', 'events/edit');
$f->verify_csrf = false;
if ($f->submit ()) {
	$e->title = $_POST['title'];
	$e->start_date = $_POST['start_date'];
	$e->end_date = $_POST['end_date'];
	$e->starts = $_POST['starts'];
	$e->ends = $_POST['ends'];
	$e->details = $_POST['details'];
	$e->address = $_POST['address'];
	$e->city = $_POST['city'];
	$e->contact = $_POST['contact'];
	$e->email = $_POST['email'];
	$e->phone = $_POST['phone'];
	$e->put ();
	Versions::add ($e);
	if (! $e->error) {
		$this->add_notification ('Event saved.');
		$_POST['id'] = $_GET['id'];
		$lock->remove ();

		require_once ('apps/events/lib/Filters.php');
		$_POST['page'] = 'events/' . $_POST['id'] . '/' . events_filter_title ($_POST['title']);
		$_POST['body'] = $_POST['details'];
		$this->hook ('events/edit', $_POST);

		if (isset ($_GET['return'])) {
			$this->redirect ($_GET['return']);
		}
		$this->redirect ('/events/admin');
	}
	$page->title = 'An Error Occurred';
	echo 'Error Message: ' . $e->error;
} else {
	$e->failed = $f->failed;
	$e = $f->merge_values ($e);
	$page->title = 'Edit Event: ' . $e->title;
	$page->head = $tpl->render ('events/edit/head', $e)
				. $tpl->render ('admin/wysiwyg');
	echo $tpl->render ('events/edit', $e);
}

?>