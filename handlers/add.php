<?php

$page->layout = 'admin';

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

$f = new Form ('post', 'events/add');
if ($f->submit ()) {
	$e = new Event ($_POST);
	$e->put ();
	Versions::add ($e);
	if (! $e->error) {
		$this->add_notification ('Event added.');
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

	$e->failed = $f->failed;
	$e = $f->merge_values ($e);
	$page->title = 'Add Event';
	$page->head = $tpl->render ('events/add/head', $e)
				. $tpl->render ('admin/wysiwyg');
	echo $tpl->render ('events/add', $e);
}

?>