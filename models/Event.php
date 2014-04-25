<?php

/**
 * Fields:
 *
 * id
 * title
 * start_date
 * end_date
 * starts
 * ends
 * details
 * address
 * city
 * contact
 * email
 * phone
 */
class Event extends Model {
	public $table = '#prefix#event';

	/**
	 * Generate a list of pages for the sitemaps app.
	 */
	public static function sitemap () {
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
	public static function search () {
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
}

?>