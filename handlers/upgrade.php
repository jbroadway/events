<?php

$page->layout = 'admin';

if (! User::require_admin ()) {
	header ('Location: /admin');
	exit;
}

if ($this->installed ('events', $appconf['Admin']['version']) === true) {
	$page->title = 'Already up-to-date';
	echo '<p><a href="/">Home</a></p>';
	return;
}

$page->title = 'Upgrading app: events';

$prefix = conf ('Database', 'prefix');
if ($prefix !== '') {
	$res = DB::shift ('select count(*) from #prefix#event');
	if ($res === false) {
		// migrate from event to #prefix#event table
		DB::beginTransaction ();

		$conn = conf ('Database', 'master');
		$driver = $conn['driver'];

		$error = false;
		$sqldata = sql_split (file_get_contents ('apps/events/conf/install_' . $driver . '.sql'));
		foreach ($sqldata as $sql) {
			if (! DB::execute ($sql)) {
				$error = DB::error ();
				DB::rollback ();
				break;
			}
		}

		if ($error) {
			echo '<p class="visible-notice">' . __ ('Error') . ': ' . $error . '</p>';
			echo '<p>' . __ ('Install failed.') . '</p>';
			return;
		}

		$res = DB::fetch ('select * from event');
		foreach ($res as $row) {
			$e = new Event ($row);
			if (! $e->put ()) {
				$error = $e->error;
				DB::rollback ();
				break;
			}
		}

		if ($error) {
			echo '<p class="visible-notice">' . __ ('Error') . ': ' . $error . '</p>';
			echo '<p>' . __ ('Install failed.') . '</p>';
			return;
		}
		
		if (! DB::execute ('drop table event')) {
			DB::rollback ();
			echo '<p class="visible-notice">' . __ ('Error') . ': ' . DB::error () . '</p>';
			echo '<p>' . __ ('Install failed.') . '</p>';
			return;
		}

		DB::commit ();
	}
}

echo '<p>Done.</p>';

$this->mark_installed ('events', $appconf['Admin']['version']);

?>