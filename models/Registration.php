<?php

namespace events;

/**
 * Fields:
 *
 * id
 * event_id
 * user_id
 * payment_id
 * ts
 * status (0 = pending, 1 = complete)
 * expires
 * num_attendees
 * attendees
 * company
 *
 * Usage:
 *
 *     // reserve a registration
 *     $registration = events\Registration::reserve ($event->id, $num_attendees);
 *
 *     // update a registration
 *     $registration->company = $company_name;
 *     $registration->attendees = json_encode ($attendees);
 *     $registration->put ();
 *
 *     // check if a reservation has expired
 *     if ($registration->expired ()) {
 *         echo 'Expired reservation.';
 *     }
 *
 *     // mark a registration paid
 *     $registration->complete ($payment->id);
 *
 *     // clear expired registration reservations for an event
 *     events\Registration::clear_expired ($event->id);
 */
class Registration extends \Model {
    public $table = '#prefix#event_registration';

    /**
	 * Clear expired registration reservations for an event.
	 */
    public static function clear_expired ($event_id) {
        $r = new Registration();
        $res = \DB::execute (
            'delete from ' . self::backticks ($r->table)
                . ' where event_id = ?'
                . ' and status = 0'
                . ' and expires <= ?',
            $event_id,
            gmdate ('Y-m-d H:i:s')
        );
        if (! $res) {
            $this->error = \DB::error ();

            return false;
        }

        return true;
    }

    /**
	 * Mark a registration as complete.
	 */
    public function complete ($payment_id = 0) {
        if (! isset ($this->data['id'])) {
            $this->error = 'Registration ID missing.';

            return false;
        }

        $this->data['payment_id'] = $payment_id;
        $this->data['status'] = 1;

        return $this->put ();
    }

    /**
	 * Has the current registration reservation expired?
	 */
    public function expired () {
        if ($this->data['expires'] <= gmdate ('Y-m-d H:i:s')) {
            return true;
        }

        return false;
    }

    /**
	 * Create a new registration reservation for the specified
	 * number of attendees. You can optionally pass a User object
	 * or it will default to `User::current ()`.
	 */
    public static function reserve ($event_id, $num_attendees = 1, $user = null) {
        $user = $user ? $user : \User::current ();

        $r = Registration::query ()
            ->where ('event_id', $event_id)
            ->where ('user_id', $user->id)
            ->where ('status', 0)
            ->single ();

        if ($r && ! $r->error) {
            $r->expires = gmdate ('Y-m-d H:i:s', time () + 900);
            $r->num_attendees = $num_attendees;
            $r->put ();

            return $r;
        }

        $r = new Registration (array (
            'event_id' => $event_id,
            'user_id' => $user->id,
            'payment_id' => 0,
            'ts' => gmdate ('Y-m-d H:i:s'),
            'status' => 0,
            'expires' => gmdate ('Y-m-d H:i:s', time () + 900),
            'num_attendees' => $num_attendees,
            'attendees' => '',
            'company' => ''
        ));
        $r->put ();

        return $r;
    }
}
