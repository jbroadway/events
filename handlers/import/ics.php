<?php

/**
 * Implements an ICS importer.
 */

$this->require_acl ('admin', 'events');

$page->layout = 'admin';
$page->title = __ ('ICS importer');

$f = new Form ('post');

if ($f->submit ()) {
	if (move_uploaded_file ($_FILES['import_file']['tmp_name'], 'cache/events_' . $_FILES['import_file']['name'])) {
		$file = 'cache/events_' . $_FILES['import_file']['name'];
		
		$imported = 0;

		$ical = new ICal ($file);
		$events = $ical->events ();
		
		$categories = events\Category::query ()->fetch_assoc ('id', 'name');
		
		foreach ($events as $event) {
			$start = strtotime ($event['DTSTART']);
			$end = strtotime ($event['DTEND']);

			// split address for city
			$address = preg_split ('/\\\\?, ?/', $event['LOCATION']);

			// normalize details
			$details = str_replace (
				array ('\\n', '\\t', '\\'),
				array ("\n",  "\t",  ''),
				$event['DESCRIPTION']
			);
			$details = (strpos ($details, '<') !== false)
				? $details
				: nl2br ($details);
			
			// split contact for name, email, phone
			$contacts = preg_split ('/\\\\?; ?/', $event['CONTACT']);
			$contact = '';
			$email = '';
			$phone = '';
			foreach ($contacts as $c) {
				if (strpos ($c, '@') !== false) {
					$email = $c;
				} elseif (preg_match ('/[.-][0-9]{4}/', $c)) {
					$phone = $c;
				} elseif ($contact === '' && strpos ($c, '://') === false) {
					// assume the first non-email/phone/url is the contact name
					$contact = $c;
				}
			}

			// determine category, create if necessary
			if (isset ($event['CATEGORIES'])) {
				$category = array_search ($event['CATEGORIES'], $categories);
				if ($category === false) {
					$c = new events\Category (array (
						'name' => $event['CATEGORIES']
					));
					$c->put ();
					$categories[$c->id] = $event['CATEGORIES'];
					$category = $c->id;
				}
			} else {
				$category = 0;
			}

			$e = new Event (array (
				'title' => $event['SUMMARY'],
				'start_date' => gmdate ('Y-m-d', $start),
				'end_date' => gmdate ('Y-m-d', $end),
				'starts' => gmdate ('H:i:s', $start),
				'ends' => gmdate ('H:i:s', $end),
				'details' => $details,
				'address' => $address[0],
				'contact' => $contact,
				'email' => $email,
				'phone' => $phone,
				'category' => $category
			));
			if (isset ($address[1])) {
				$e->city = $address[1];
			}

			if ($e->put ()) {
				Versions::add ($e);
				$imported++;
			}
		}
			
		echo '<p>' . __ ('Imported %d events.', $imported) . '</p>';
		echo '<p><a href="/events/admin">' . __ ('Continue') . '</a></p>';
		return;
	} else {
		echo '<p><strong>' . __ ('Error uploading file.') . '</strong></p>';
	}
}

$o = new StdClass;

echo $tpl->render ('events/import/ics', $o);
