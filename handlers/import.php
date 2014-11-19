<?php

/**
 * Lists available importers.
 */

$this->require_acl ('admin', 'events');

$page->layout = 'admin';

$page->title = __ ('Choose an importer');

echo $tpl->render ('events/import');
