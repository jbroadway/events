<?php

/**
 * List of event categories.
 */

$page->layout = 'admin';

$this->require_acl ('admin', 'events');

$categories = events\Category::query ()->order ('name', 'asc')->fetch_orig ();

$page->title = __ ('Event Categories');

echo $tpl->render (
	'events/categories',
	array (
		'categories' => $categories
	)
);

?>