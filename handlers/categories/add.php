<?php

$page->layout = 'admin';

$this->require_acl ('admin', 'admin/add', 'events');

$page->title = __ ('Add Category');

$form = new Form ('post', $this);

echo $form->handle (function ($form) {
	$c = new events\Category (array ('name' => $_POST['name']));
	$c->put ();
	if ($c->error) {
		$form->controller->add_notification (__ ('Error adding category.'));
	} else {
		$form->controller->add_notification (__ ('Category added.'));
	}
	$form->controller->redirect ('/events/categories');
});
