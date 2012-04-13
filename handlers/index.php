<?php

if (count ($this->params) > 0) {
	require_once ('apps/events/lib/Filters.php');

	$e = new Event ($this->params[0]);
	if ($e->error) {
		$page->title = i18n_get ('Event not found');
		printf ('<p><a href="/events">&laquo; %s</a></p>', i18n_get ('Back'));
		return;
	}

	$page->title = $e->title;
	$e->details = $tpl->run_includes ($e->details);
	echo $tpl->render ('events/event', $e->orig ());
} else {
	if (! $this->internal) {
		$page->title = i18n_get ('Events');
	}
	$page->add_script ('<script src="/apps/events/js/fullcalendar/fullcalendar.min.js"></script>');
	$page->add_script ('<link rel="stylesheet" type="text/css" href="/apps/events/js/fullcalendar/fullcalendar.css" />');
	echo $tpl->render ('events/index');
}

?>