<?php

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

$page->title = 'Events';
$page->layout = 'admin';

?>