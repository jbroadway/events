<?php

$page->layout = 'admin';

$this->require_acl ('admin', 'events');

if (! isset ($_GET['id'])) {
    $this->redirect ('/events/admin');
}

$event = new Event ($_GET['id']);
if ($event->error) {
    $this->redirect ('/events/admin');
}

$event = $event->orig ();
$event->guests = array ();

$reg = events\Registration::query ()
    ->where ('event_id', $event->id)
    ->where ('status != 0')
    ->order ('status', 'desc')
    ->fetch_orig ();

foreach ($reg as $k => $registration) {
	$reg[$k]->guests = json_decode ($registration->attendees);
}

$event->registrations = $reg;

$page->title = sprintf (
    '%s: %s (%s)',
    __ ('Guests'),
    Template::sanitize ($event->title),
    events\Filter::date ($event->start_date)
);

echo $tpl->render ('events/guests', $event);
