<?php

/**
 * Create a preview of an event based on POST data sent to it.
 * POST data must match values available to the Event object.
 */

$this->require_admin ();

require_once ('apps/events/lib/Filter.php');

$e = new Event ($_POST);

$page->id = 'events';
$page->title = $e->title;
$page->layout = Appconf::events ('Events', 'event_layout');
$e->details = $tpl->run_includes ($e->details);
$e->remaining = $e->available;
if ($e->end_date === '' || $e->end_date === '0000-00-00' || $e->end_date === $e->start_date) {
	$e->end_date = false;
}
$e->has_passed = ($e->start_date . ' ' . $e->starts) < gmdate ('Y-m-d H:i:s');

$this->run ('admin/util/minimal-grid');
$page->add_style ('/apps/events/css/events.css');

echo $tpl->render ('events/event', $e->orig ());

?>