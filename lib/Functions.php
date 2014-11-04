<?php

function events_yes_no () {
    return array (
        (object) array ('key' => 'no', 'value' => __ ('No')),
        (object) array ('key' => 'yes', 'value' => __ ('Yes'))
    );
}

function events_categories () {
	$out = array ((object) array ('key' => '', 'value' => __ ('None')));
	$cats = events\Category::query ()
		->order ('name', 'asc')
		->fetch_assoc ('id', 'name');
	
	foreach ($cats as $id => $name) {
		$out[] = (object) array (
			'key' => $id,
			'value' => $name
		);
	}
	
	return $out;
}
