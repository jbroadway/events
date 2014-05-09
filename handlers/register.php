<?php

if (count ($this->params) === 0) {
	$page->title = __ ('Event not found');
	printf ('<p><a href="/events">&laquo; %s</a></p>', __ ('Back'));
	return;
}

$e = new Event ($this->params[0]);
if ($e->error) {
	$page->title = __ ('Event not found');
	printf ('<p><a href="/events">&laquo; %s</a></p>', __ ('Back'));
	return;
}

$e->remaining = $e->available ();
$e->options = range (1, ($e->remaining < 12) ? $e->remaining : 12);

if (User::require_login ()) {
	$r = events\Registration::query ()
		->where ('event_id', $e->id)
		->where ('user_id', User::val ('id'))
		->where ('status', 0)
		->single ();
} else {
	$r = false;
}

if (! $r || $r->error || $r->expires <= gmdate ('Y-m-d H:i:s')) {
	$e->company = '';
	$e->reservation_id = 0;
	$e->num_attendees = 0;
	$e->attendees = '[]';
} else {
	$e->company = $r->company;
	$e->reservation_id = $r->id;
	$e->num_attendees = $r->num_attendees;
	if (strlen ($r->attendees) > 0) {
		$e->attendees = $r->attendees;
	} else {
		$e->attendees = '[]';
	}
}

if ($e->price > 0) {
	//$this->force_https ();
}

$page->title = __ ('Event registration') . ': ' . $e->title;
$page->add_script ('/apps/events/js/registration.js');
echo $tpl->render ('events/register', $e->orig ());

// housekeeping...
events\Registration::clear_expired ($e->id);

?>