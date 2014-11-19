<?php

namespace events;

class API extends \Restful
{
    /**
	 * Reserve a specified number of tickets.
	 *
	 *     POST /events/api/registration/reserve/<event-id>/<num-attendees>
	 */
	public function post_reserve ($event_id, $num_attendees) {
		if (! \User::require_login ()) {
			return $this->error ('Unauthorized');
		}

		$registration = Registration::reserve ($event_id, $num_attendees);
		if ($registration->error) {
			return $this->error ('Reservation failed');
		}
		$reg = $registration->orig ();
		$reg->timer = strtotime ($reg->expires) - time ();
		return $reg;
	}

	/**
	 * Check if a reservation has expired.
	 *
	 *     GET /events/api/registration/expired/<registration-id>
	 */
    public function get_expired($registration_id)
    {
        if (! \User::require_login ()) {
            return $this->error ('Unauthorized');
        }

        $registration = new Registration ($registration_id);
        if ($registration->error) {
            return $this->error ('Reservation not found');
        }

        return $registration->expired ();
    }

    /**
	 * Update a registration with company name and attendees.
	 *
	 *     POST /events/api/registration/update/<registration-id>
	 *
	 * Parameters:
	 *
	 * - company
	 * - attendees (array of attendee names)
	 */
    public function post_update($registration_id)
    {
        if (! \User::require_login ()) {
            return $this->error ('Unauthorized');
        }

        $registration = new Registration ($registration_id);
        if ($registration->error) {
            return $this->error ('Reservation not found');
        }

        if (isset ($_POST['company'])) {
            $registration->company = $_POST['company'];
        }

        if (isset ($_POST['attendees'])) {
            if (! is_array ($_POST['attendees'])) {
                return $this->error ('Attendees should be an array');
            }
            if (count ($_POST['attendees']) != $registration->num_attendees) {
                return $this->error ('Incorrect number of attendees');
            }
            $registration->attendees = json_encode ($_POST['attendees']);
        }

        if (! $registration->put ()) {
            error_log ('[events/api/registration/update] ' . $registration->error);

            return $this->error ('Update failed');
        }

        return true;
    }

    /**
	 * Mark a registration as completed.
	 *
	 *     POST /events/api/registration/complete/<registration-id>[/<payment-id>]
	 */
    public function post_complete($registration_id, $payment_id = 0)
    {
        if (! \User::require_login ()) {
            return $this->error ('Unauthorized');
        }

        $registration = new Registration ($registration_id);
        if ($registration->error) {
            return $this->error ('Reservation not found');
        }

        $event = new \Event ($registration->event_id);
        if ($event->error) {
            return $this->error ('Event not found');
        }

        $res = $registration->complete ($payment_id);
        if (! $res) {
            return $this->error ('Registration failed');
        }

        try {
            $r = $registration->orig ();
            $r->attendees = json_decode ($r->attendees);
            $r->event = $event->orig ();
            $r->user = \User::current ()->orig ();

            if ($r->payment_id) {
                $r->subtotal = $r->event->price * $r->num_attendees;
            }

            \Mailer::send (array (
                'to' => array (\User::val ('email'), \User::val ('name')),
                'subject' => 'Event registration confirmation: ' . $event->title,
                'text' => $this->controller->template ()->render ('events/email/confirmation', $r)
            ));
            
            $to = ! empty ($event->email) ? $event->email : conf ('General', 'email_from');

			\Mailer::send (array (
				'to' => $to,
				'subject' => 'Event registration notification: ' . $event->title,
				'text' => $this->controller->template ()->render ('events/email/notification', $r)
			));
        } catch (\Exception $e) {
        }

        return $res;
    }
}
