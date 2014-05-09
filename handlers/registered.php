<?php

$this->require_login ();

if (! isset ($this->params[0]) || ! isset ($this->params[1])) {
	$this->redirect ('/events');
}

$event = new Event ($this->params[0]);
if ($event->error) {
	$page->title = __ ('Event not found');
	printf ('<p><a href="/events">&laquo; %s</a></p>', __ ('Back'));
	return;
}

$registration = new events\Registration ($this->params[1]);
if ($registration->error) {
	$page->title = __ ('Registration not found');
	printf ('<p><a href="/events">&laquo; %s</a></p>', __ ('Back'));
	return;
}

if ($registration->event_id != $event->id || $registration->user_id != User::val ('id')) {
	$page->title = __ ('Invalid registration');
	printf ('<p><a href="/events">&laquo; %s</a></p>', __ ('Back'));
	return;
}

if ($registration->status == 0) {
	$page->title = __ ('Incomplete registration');
	printf ('<p><a href="/events">&laquo; %s</a></p>', __ ('Back'));
	return;
}

require_once ('apps/events/lib/Filters.php');

$page->title = __ ('Event registration: ' . $event->title);

$reg = $registration->orig ();
$reg->attendees = json_decode ($reg->attendees);
$reg->event = $event->orig ();
$reg->user = User::current ()->orig ();

if ($reg->payment_id) {
	$reg->subtotal = $reg->event->price * $reg->num_attendees;
}

echo $tpl->render (
	'events/registered',
	$reg
);

?>