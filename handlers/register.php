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
	
$discount = events\App::discount ();
$allow_invoice = events\App::allow_invoice ();

$e->remaining = $e->available ();
$e->options = range (1, ($e->remaining < 12) ? $e->remaining : 12);

$e->discount = $discount;
$e->discount_price = $e->discount_price ($discount);
$e->allow_invoice = $allow_invoice;

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

// handle invoice-me or checkout
if ($e->price > 0 && $r !== false && isset ($this->params[1])) {

	if ($this->params[1] === 'invoice' && $allow_invoice) {
		// email admin
		try {
			$user = User::current ();
			
			$reg = $r->orig ();
			$reg->attendees = json_decode ($r->attendees);
			$reg->event = $e->orig ();
			$reg->user = $user->orig ();
			$reg->subtotal = $e->discount_price * $reg->num_attendees;

			Mailer::send (array (
				'to' => conf ('General', 'email_from'),
				'subject' => 'Invoice requested for event: ' . $e->title,
				'text' => View::render ('events/email/invoice', $reg)
			));
			
			if (! $r->complete (0)) {
				error_log (DB::error ());
				echo $this->error (404, __ ('An error occurred'), __ ('There was an error in the event registration. Please contact the administrator of the site to assist you.'));
				return;
			}

			$this->redirect ('/events/registered/' . $e->id . '/' . $r->id);
		} catch (Exception $e) {
			error_log ('Mail error: ' . $e->getMessage ());

			echo $this->error (404, __ ('An error occurred'), __ ('There was an error requesting an invoice. Please contact the administrator of the site to assist you.'));
			return;
		}

	} elseif ($this->params[1] === 'checkout') {
		// clone controller to pass to closure
		$c = $this;

		// checkout summary
		$page->title = __ ('Event registration') . ': ' . $e->title;
		echo $tpl->render ('events/checkout', $e->orig ());
			
		// payment handler
		echo $this->run (
			Appconf::events ('Events', 'payment_handler'),
			array (
				'amount' => $e->discount_price * $r->num_attendees * 100,
				'description' => $e->title,
				'callback' => function ($charge, $payment) use ($r, $e, $c, $tpl) {
					$r->complete ($payment->id);

					try {
						$r = $r->orig ();
						$r->attendees = json_decode ($r->attendees);
						$r->event = $e->orig ();
						$r->user = User::current ()->orig ();
						$r->subtotal = $e->discount_price * $r->num_attendees;

						Mailer::send (array (
							'to' => array (User::val ('email'), User::val ('name')),
							'subject' => 'Event registration confirmation: ' . $e->title,
							'text' => $tpl->render ('events/email/confirmation', $r)
						));
			
						$to = ! empty ($e->email) ? $e->email : conf ('General', 'email_from');

						Mailer::send (array (
							'to' => $to,
							'subject' => 'Event registration notification: ' . $e->title,
							'text' => $tpl->render ('events/email/notification', $r)
						));
					} catch (\Exception $exception) {
					}

					$c->redirect ('/events/registered/' . $e->id . '/' . $r->id);
				}
			)
		);

		return;
	}
}

$data = $e->orig ();
if ($r) {
	$current = time ();
	$expires = strtotime ($r->expires);
	$data->timer = $expires - $current;
} else {
	$data->timer = 0;
}

echo $tpl->render ('events/register', $data);

// housekeeping...
events\Registration::clear_expired ($e->id);
