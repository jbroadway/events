<?php

$this->require_acl ('admin', 'events');

if (! isset ($_GET['id'])) {
    $this->redirect ('/events/admin');
}

$event = new Event ($_GET['id']);
if ($event->error) {
    $this->redirect ('/events/admin');
}

$event = $event->orig ();

$reg = events\Registration::query ()
    ->where ('event_id', $event->id)
    ->where ('status', 1)
    ->fetch_orig ();

$page->layout = false;
header ('Cache-control: private');
header ('Content-Type: text/plain');
header ('Content-Disposition: attachment; filename=' . URLify::filter ($event->title) . '-' . $event->start_date . '.csv');

echo "\"Guest name\",\"Company name\",\"Registered by\",\"Registrant email\",\"Status\",\"Notes\"\n";

foreach ($reg as $registration) {
    $guests = json_decode ($registration->attendees);
    $u = new User ($registration->user_id);
    foreach ($guests as $guest) {
        printf (
            "\"%s\",\"%s\",\"%s\",\"%s\"\n",
            $guest,
            $registration->company,
            $u->name,
            $u->email,
            events\Filter::status ($registration->status),
            $registration->notes
        );
    }
}
