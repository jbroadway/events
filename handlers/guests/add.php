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

$page->title = __ ('Add Guest');

$form = new Form ('post', $this);

echo $form->handle (function ($form) use ($event, $page, $tpl) {
	$reg = new events\Registration (array (
		'event_id' => $event->id,
		'user_id' => $_POST['user'],
		'payment_id' => 0,
		'ts' => gmdate ('Y-m-d H:i:s'),
		'status' => 1,
		'expires' => gmdate ('Y-m-d H:i:s'),
		'num_attendees' => 1,
		'attendees' => json_encode (array (User::field ($_POST['user'], 'name'))),
		'company' => $_POST['company']
	));
	$reg->put ();
	if ($reg->error) {
		$page->title = __ ('An Error Occurred');
		printf ('<p>%s</p>', __ ('Unable to add guest to this event.'));
		printf ('<p><a href="/events/guests?id=%d">%s</a></p>', $event->id, __ ('Continue'));
		return;
	}
	$form->controller->add_notification (__ ('Guest added.'));
	$form->controller->redirect ('/events/guests?id=' . $event->id);
});
