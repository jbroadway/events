<?php

/**
 * Fields:
 *
 * - id
 * - title
 * - start_date
 * - end_date
 * - starts
 * - ends
 * - details
 * - address
 * - city
 * - contact
 * - email
 * - phone
 * - price
 * - available
 * - category
 * - thumbnail
 */
class Event extends Model {
    public $table = '#prefix#event';

    /**
	 * Return the number of available registrations for the current event.
	 * Takes the registration reservations into account.
	 */
    public function available () {
        if ($this->data['available'] == 0) {
            return $this->data['available'];
        }

        $num = DB::shift (
            'select sum(num_attendees) from #prefix#event_registration
			 where event_id = ? and
			 status = 1 or
			 (status = 0 and expires > ?)',
             $this->data['id'],
             gmdate ('Y-m-d H:i:s')
        );

        return $this->data['available'] - $num;
    }

    /**
	 * Return the number of confirmed guests for several events from an
	 * array of event IDs. Does not calculate availability and does not
	 * consider registrations that are in progress.
	 */
    public static function guests ($ids) {
        return DB::pairs (
            'select event_id, sum(num_attendees) from #prefix#event_registration
			where event_id in(' . join (', ', $ids) . ')
			and status > 0
			group by event_id'
        );
    }

    /**
	 * Generate a list of pages for the sitemaps app.
	 */
    public static function sitemap ()  {
        $events = Event::query ()
            ->where ('start_date >= "' . gmdate ('Y-m-01 00:00:00') . '"')
            ->where ('end_date <= "' . gmdate ('Y-m-t 23:59:59') . '"')
            ->fetch_orig ();

        $urls = array ();
        foreach ($events as $event) {
            $urls[] = sprintf ('/events/%d/%s', $event->id, URLify::filter ($event->title));
        }

        return $urls;
    }

    /**
	 * Generate a list of events for the search app,
	 * and add them directly via `Search::add()`.
	 */
    public static function search ()  {
        $events = self::query ()
            ->fetch_orig ();

        foreach ($events as $i => $event) {
            $url = 'events/' . $event->id . '/' . URLify::filter ($event->title);
            if (! Search::add (
                $url,
                array (
                    'title' => $event->title,
                    'text' => $event->details,
                    'url' => '/' . $url
                )
            )) {
                return array (false, $i);
            }
        }

        return array (true, count ($events));
    }
	
	/**
	 * Get attended events by user.
	 */
	public static function by_user ($user) {
		return self::query ('e.id, e.title, e.start_date, r.status, r.num_attendees, r.notes')
			->from ('#prefix#event e, #prefix#event_registration r')
			->where ('r.event_id = e.id')
			->where ('r.user_id', $user)
			->order ('e.title', 'asc')
			->fetch_orig ();
	}
	
	/**
	 * Get the event's discount price based on the specified discount
	 * value, which is an integer.
	 */
	public function discount_price ($discount = 0) {
		if ($discount > 0) {
			return $this->price - ($this->price * ($discount / 100));
		}
		return $this->price;
	}
}
