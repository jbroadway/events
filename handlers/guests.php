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
    ->where ('status', 1)
    ->fetch_orig ();

foreach ($reg as $registration) {
    $guests = json_decode ($registration->attendees);
    foreach ($guests as $guest) {
        $event->guests[] = (object) array (
            'name' => $guest,
            'company' => $registration->company
        );
    }
}

$page->title = sprintf (
    '%s: %s (%s)',
    __ ('Guests'),
    $event->title,
    events\Filter::date ($event->start_date)
);

echo $tpl->render ('events/guests', $event);
