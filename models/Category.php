<?php

namespace events;

/**
 * Fields:
 *
 * - id
 * - name
 *
 * Usage:
 *
 *     // fetch all as associative array
 *     $categories = events\Category::query ()
 *         ->order ('name', 'asc')
 *         ->fetch_assoc ('id', 'name');
 *
 *     // get all events by category
 *     $category = new events\Category ($category_id);
 *     $events = $category->events ();
 */
class Category extends \Model {
	public $table = '#prefix#event_category';
	
	public $fields = array (
		'events' => array (
			'has_many' => 'Event',
			'field_name' => 'category',
			'order_by' => array ('start_date', 'desc')
		)
	);

	public static $categories = null;

	public static function filter_name ($id) {
		if (self::$categories === null) {
			self::$categories = self::query ()
				->order ('name', 'asc')
				->fetch_assoc ('id', 'name');
		}
		
		return isset (self::$categories[$id]) ? self::$categories[$id] : false;
	}
}

?>