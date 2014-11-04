<?php

$page->layout = 'admin';

$this->require_acl ('admin', 'admin/delete', 'events');

$c = new events\Category ($_POST['id']);

if (! $c->remove ()) {
	$this->add_notification (__ ('Error deleting category.'));
	$this->redirect ('/events/categories');
}

$this->add_notification (__ ('Category deleted.'));
$this->redirect ('/events/categories');

?>