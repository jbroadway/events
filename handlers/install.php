<?php

$page->layout = 'admin';

if (! User::require_admin ()) {
	header ('Location: /admin');
	exit;
}

$cur = $this->installed ('events', $appconf['Admin']['version']);

if ($cur === true) {
	$page->title = __ ('Already installed');
	echo '<p><a href="/events/admin">' . __ ('Continue') . '</a></p>';
	return;
} elseif ($cur !== false) {
	header ('Location: /' . $appconf['Admin']['upgrade']);
	exit;
}

$page->title = __ ('Installing App') . ': ' . __ ('Events');

if (ELEFANT_VERSION < '1.1.0') {
	$driver = conf ('Database', 'driver');
} else {
	$conn = conf ('Database', 'master');
	$driver = $conn['driver'];
}

$error = false;
$sqldata = sql_split (file_get_contents ('apps/events/conf/install_' . $driver . '.sql'));
foreach ($sqldata as $sql) {
	if (! DB::execute ($sql)) {
		$error = DB::error ();
		echo '<p class="notice">' . __ ('Error') . ': ' . DB::error () . '</p>';
		break;
	}
}

if ($error) {
	echo '<p class="notice">' . __ ('Error') . ': ' . $error . '</p>';
	echo '<p>' . __ ('Install failed.') . '</p>';
	return;
}

echo '<p><a href="/events/admin">' . __ ('Done.') . '</a></p>';

$this->mark_installed ('events', $appconf['Admin']['version']);

?>