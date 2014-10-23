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
$e->has_passed = ($e->start_date . ' ' . $e->starts) < gmdate ('Y-m-d H:i:s');
echo $tpl->render ('events/event', $e->orig ());

?>