<?php

$page->layout = 'admin';

$this->require_acl ('admin', 'admin/edit', 'events');

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
	$e->end_date = (! empty ($_POST['end_date'])) ? $_POST['end_date'] : $_POST['start_date'];
	$e->starts = $_POST['starts'];
	$e->ends = $_POST['ends'];
	$e->details = $_POST['details'];
	$e->venue = $_POST['venue'];
	$e->address = $_POST['address'];
	$e->city = $_POST['city'];
	$e->contact = $_POST['contact'];
	$e->email = $_POST['email'];
	$e->phone = $_POST['phone'];
	$e->available = (! empty ($_POST['available'])) ? $_POST['available'] : 0;
	$e->price = (! empty ($_POST['price'])) ? $_POST['price'] : 0;
	$e->thumbnail = $_POST['thumbnail'];
	$e->category = $_POST['category'];
	$e->access = $_POST['access'];
	$e->put ();
	Versions::add ($e);
	if (! $e->error) {
		$this->add_notification ('Event saved.');
		$_POST['id'] = $_GET['id'];
		$lock->remove ();

		$_POST['page'] = 'events/' . $_POST['id'] . '/' . URLify::filter ($_POST['title']);
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
	$e->categories = events\Category::query ()
		->order ('name', 'asc')
		->fetch_assoc ('id', 'name');

	$e = $f->merge_values ($e);
	$page->title = 'Edit Event: ' . Template::sanitize ($e->title);
	$this->run ('admin/util/wysiwyg');
	echo $tpl->render ('events/edit/head', $e);
	echo $tpl->render ('events/edit', $e);
}
