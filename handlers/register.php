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

$page->title = __ ('Event registration') . ': ' . $e->title;
$page->add_script ('/apps/events/js/registration.js');

if ($e->price > 0 && $r !== false && isset ($this->params[1]) && $this->params[1] === 'checkout') {
	// clone controller to pass to closure
	$c = $this;
	
	// checkout summary
	$page->title = __ ('Event registration') . ': ' . $e->title;
	echo $tpl->render ('events/checkout', $e->orig ());

	// payment handler
	echo $this->run (
		Appconf::events ('Events', 'payment_handler'),
		array (
			'amount' => $e->price * $r->num_attendees * 100,
			'description' => $e->title,
			'callback' => function ($charge, $payment) use ($r, $e, $c, $tpl) {
				$r->complete ($payment->id);
				
				try {
					$r = $r->orig ();
					$r->attendees = json_decode ($r->attendees);
					$r->event = $e->orig ();
					$r->user = User::current ()->orig ();
					$r->subtotal = $e->price * $r->num_attendees;
					
					Mailer::send (array (
						'to' => array (User::val ('email'), User::val ('name')),
						'subject' => 'Event registration confirmation: ' . $e->title,
						'text' => $tpl->render ('events/email/confirmation', $r)
					));
				} catch (Exception $e) {
				}

				$c->redirect ('/events/registered/' . $e->id . '/' . $r->id);
			}
		)
	);
	return;
}

echo $tpl->render ('events/register', $e->orig ());

// housekeeping...
events\Registration::clear_expired ($e->id);

?>