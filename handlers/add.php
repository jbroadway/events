<?php

$page->layout = 'admin';

$this->require_acl ('admin', 'admin/add', 'events');

$f = new Form ('post', 'events/add');
$f->verify_csrf = false;
if ($f->submit ()) {
    $_POST['end_date'] = (! empty ($_POST['end_date'])) ? $_POST['end_date'] : $_POST['start_date'];
    $_POST['available'] = (! empty ($_POST['available'])) ? $_POST['available'] : 0;
    $_POST['price'] = (! empty ($_POST['price'])) ? $_POST['price'] : 0;

    $e = new Event ($_POST);
    $e->put ();
    Versions::add ($e);
    if (! $e->error) {
        $this->add_notification ('Event added.');

        $_POST['page'] = 'events/' . $e->id . '/' . URLify::filter ($_POST['title']);
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
	$e->available = 0;
	$e->price = '0.00';
	$e->categories = events\Category::query ()
		->order ('name', 'asc')
		->fetch_assoc ('id', 'name');

	$e->failed = $f->failed;
	$e = $f->merge_values ($e);
	$page->title = 'Add Event';
	$this->run ('admin/util/wysiwyg');
	echo $tpl->render ('events/add/head', $e);
	echo $tpl->render ('events/add', $e);
}
