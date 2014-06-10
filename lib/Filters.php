<?php

function events_filter_shortdate ($d) {
	return I18n::short_date ($d);
}

function events_filter_date ($d) {
	return I18n::day_date ($d);
}

function events_filter_time ($t) {
	return I18n::time ($t);
}

?>