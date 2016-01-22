<?php

/**
 * Duplicate an event.
 */

$this->require_acl ('admin', 'events');

$e = new Event ($_GET['id']);

$data = $e->orig ();
unset ($data->id);
$data->title .= ' (copy)';
$data->access = 'private';

$e2 = new Event ($data);
$e2->put ();

$this->redirect ('/events/edit?id=' . $e2->id);
