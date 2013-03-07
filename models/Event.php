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
}

?>