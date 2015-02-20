<?php

$this->require_acl ('admin', 'user', 'events');

if (! $this->internal) return;

$events = Event::by_user ($this->data['user']);

echo $tpl->render ('events/user', array (
	'events' => $events
));
