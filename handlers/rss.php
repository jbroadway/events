<?php

/**
 * Renders the RSS feed for the blog.
 */

$res = $cache->get ('events_rss');
if (! $res) {
    $feed = new StdClass();
    $feed->title = $appconf['Events']['title'];
    $feed->date = gmdate ('Y-m-d\TH:i:s');
    $feed->events = Event::query ()
        ->where ('start_date >= "' . gmdate ('Y-m-d') . '"')
        ->order ('start_date', 'asc')
        ->fetch_orig (10);

    foreach ($feed->events as $k => $event) {
        $feed->events[$k]->url = '/events/' . $event->id . '/' . URLify::filter ($event->title);
        $feed->events[$k]->body = $tpl->run_includes ($feed->events[$k]->details);
    }

    $res = $tpl->render ('events/rss', $feed);
    $cache->set ('events_rss', $res, 1800); // half an hour
}
$page->layout = false;
header ('Content-Type: text/xml');
echo $res;
