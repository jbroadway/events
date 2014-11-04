<?php

$page->layout = 'admin';

$this->require_acl ('admin', 'admin/edit', 'events');

if (! isset ($_GET['id'])) {
	$this->redirect ('/events/categories');
}

$c = new events\Category ($_GET['id']);

if ($c->error) {
	$this->redirect ('/events/categories');
}

$page->title = __ ('Add Category');

$form = new Form ('post', $this);

$form->data = $c->orig ();

echo $form->handle (function ($form) {
	$c = new events\Category ($_GET['id']);
	$c->name = $_POST['name'];
	$c->put ();
	if ($c->error) {
		$form->controller->add_notification (__ ('Error updating category.'));
	} else {
		$form->controller->add_notification (__ ('Category saved.'));
	}
	$form->controller->redirect ('/events/categories');
});
