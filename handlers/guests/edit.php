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

$reg = new events\Registration ($_GET['registration']);
if ($event->error) {
    $this->redirect ('/events/guests?id=' . $_GET['id']);
}

$page->title = __ ('Update Registration');

$form = new Form ('post', $this);

$form->data = $reg->orig ();
$form->data->guests = json_decode ($reg->attendees);

echo $form->handle (function ($form) use ($event, $page, $tpl, $reg) {
	$reg->company = $_POST['company'];
	$reg->status = $_POST['status'];
	$reg->notes = $_POST['notes'];
	$reg->put ();
	if ($reg->error) {
		$page->title = __ ('An Error Occurred');
		printf ('<p>%s</p>', __ ('Unable to update registration info.'));
		printf ('<p><a href="/events/guests?id=%d">%s</a></p>', $event->id, __ ('Continue'));
		return;
	}
	$form->controller->add_notification (__ ('Registration updated.'));
	$form->controller->redirect ('/events/guests?id=' . $event->id);
});
