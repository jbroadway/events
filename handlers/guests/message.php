<?php

$this->require_acl ('admin', 'events');

$event = new Event ($_GET['id']);

$page->title = sprintf (
    '%s: %s (%s)',
    __ ('Message Registrants'),
    $event->title,
    events\Filter::date ($event->start_date)
);
$page->layout = 'admin';

$form = new Form ('post', $this);

$form->data = array (
	'id' => $_GET['id']
);

echo $form->handle (function ($form) use ($page, $list) {
	$members = events\Registration::query ('distinct u.id, u.name, u.email')
		->from ('#prefix#event_registration r, #prefix#user u')
		->where ('r.event_id', $_GET['id'])
		->where ('r.user_id = u.id')
		->where ('r.status > 0')
		->fetch_orig ();

	$count = 0;

	try {
		set_time_limit (0);

		foreach ($members as $member) {
			Mailer::send (array (
				'to' => array ($member->email, $member->name),
				'subject' => $_POST['subject'],
				'text' => $_POST['body']
			));

			$count++;
		}
	} catch (Exception $e) {
		error_log ('Error sending message: ' . $e->getMessage ());
		$form->failed[] = 'email';
		return false;
	}
	
	$form->controller->add_notification (__ ('%d messages sent.', $count));
	$form->controller->redirect ('/events/guests?id=' . Template::sanitize ($_GET['id']));
});
