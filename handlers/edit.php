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
if ($f->submit ()) {
	$e->title = $_POST['title'];
	$e->details = $_POST['details'];
	// etc.
	$e->put ();
	Versions::add ($e);
	if (! $e->error) {
		$this->add_notification ('Event saved.');
		$_POST['id'] = $_GET['id'];
		$lock->remove ();
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