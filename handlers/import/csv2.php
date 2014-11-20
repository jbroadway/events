<?php

/**
 * Finishes importing a CSV file.
 */

$this->require_acl ('admin', 'events');

$page->layout = 'admin';
$page->title = __ ('CSV Importer');

$imported = 0;

$file = 'cache/events_csv_import.csv';

if (! file_exists ($file)) {
	echo '<p>' . __ ('Uploaded CSV file not found.') . '</p>';
	echo '<p><a href="/events/import">' . __ ('Back') . '</a></p>';
	return;
}

$res = blog\CsvParser::parse ($file);
if (! $res) {
	echo '<p>' . __ ('Unable to parse the uploaded file.') . '</p>';
	echo '<p><a href="/events/import">' . __ ('Back') . '</a></p>';
	return;
}

// Map fields
$title = false;
$start_date = false;
$end_date = false;
$starts = false;
$ends = false;
$category = false;
$details = false;
$venue = false;
$address = false;
$city = false;
$contact = false;
$email = false;
$phone = false;

foreach ($_POST as $k => $v) {
	if (strpos ($k, 'map-') === 0 && $v !== '') {
		$n = (int) str_replace ('map-', '', $k);
		${$v} = $n;
	}
}

// Remove first line
array_shift ($res);

$categories = events\Category::query ()->fetch_assoc ('id', 'name');

foreach ($res as $row) {
	if ($category !== false) {
		$cat = array_search ($categories, $row[$category]);
		if ($cat === false) {
			$c = new events\Category (array (
				'name' => $row[$category]
			));
			$c->put ();
			$categories[$c->id] = $row[$category];
			$row[$category] = $c->id;
		}
	}

	$event = array (
		'title' => ($title !== false) ? $row[$title] : '',
		'start_date' => ($start_date !== false) ? gmdate ('Y-m-d', strtotime ($row[$start_date])) : '',
		'end_date' => ($end_date !== false) ? gmdate ('Y-m-d', strtotime ($row[$end_date])) : '',
		'starts' => ($starts !== false) ? gmdate ('H:i:s', strtotime ($row[$starts])) : '',
		'ends' => ($ends !== false) ? gmdate ('H:i:s', strtotime ($row[$ends])) : '',
		'category' => ($category !== false) ? $row[$category] : 0,
		'details' => ($details !== false) ? $row[$details] : '',
		'venue' => ($venue !== false) ? $row[$venue] : '',
		'address' => ($address !== false) ? $row[$address] : '',
		'city' => ($city !== false) ? $row[$city] : '',
		'contact' => ($contact !== false) ? $row[$contact] : '',
		'email' => ($email !== false) ? $row[$email] : '',
		'phone' => ($phone !== false) ? $row[$phone] : '',
		'access' => ($_POST['public'] === 'yes') ? 'public' : 'private'
	);

	$e = new Event ($event);
	if ($e->put ()) {
		Versions::add ($e);
		$imported++;
	}
}

echo '<p>' . __ ('Imported %d events.', $imported) . '</p>';
echo '<p><a href="/events/admin">' . __ ('Continue') . '</a></p>';
