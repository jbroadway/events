<?php

$page->add_style (sprintf (
    '<link rel="alternate" type="application/rss+xml" href="http://%s/events/rss" />',
    Appconf::admin ('Site Settings', 'site_domain')
));

if (count ($this->params) > 0 && is_numeric ($this->params[0])) {
    $e = new Event ($this->params[0]);
    if ($e->error || ($e->access !== 'public' && ! User::access ($e->access))) {
        $page->title = __ ('Event not found');
        printf ('<p><a href="/events">&laquo; %s</a></p>', __ ('Back'));

        return;
    }

    $page->id = 'events';
    $page->title = Template::sanitize ($e->title);
    $page->layout = $appconf['Events']['event_layout'];
    $this->run ('admin/util/minimal-grid');
	$page->add_style ('/apps/events/css/events.css');
	
	$discount = events\App::discount ();
	$allow_invoice = events\App::allow_invoice ();

    $e->details = $tpl->run_includes ($e->details);
    $e->remaining = $e->available ();
    $e->discount = $discount;
    $e->discount_price = $e->discount_price ($discount);
    $e->allow_invoice = $allow_invoice;
    if ($e->end_date === '' || $e->end_date === '0000-00-00' || $e->end_date === $e->start_date) {
        $e->end_date = false;
    }
    $e->has_passed = ($e->start_date . ' ' . $e->starts) < gmdate ('Y-m-d H:i:s');

	// add opengraph/twitter card meta tags
	require_once ('apps/blog/lib/Filters.php');
	$url = ($this->is_https () ? 'https' : 'http') . '://' . Appconf::admin ('Site Settings', 'site_domain') . '/events/' . $e->id . '/' . URLify::filter ($e->title);
	$desc = blog_filter_truncate (strip_tags ($e->details), 300);

	$page->add_meta ('og:type', 'article', 'property');
	$page->add_meta ('og:site_name', conf ('General', 'site_name'), 'property');
	$page->add_meta ('og:title', $e->title, 'property');
	$page->add_meta ('og:description', $desc, 'property');
	$page->add_meta ('og:url', $url, 'property');

	if ($e->thumbnail !== '') {
		$page->add_meta (
			'og:image',
			($this->is_https () ? 'https' : 'http') . '://'. Appconf::admin ('Site Settings', 'site_domain') . $e->thumbnail,
			'property'
		);
	}

	$page->add_meta ('twitter:card', 'summary_large_image');
	$twitter_id = Appconf::user ('Twitter', 'twitter_id');
	if (is_string ($twitter_id) && $twitter_id !== '') {
		$page->add_meta ('twitter:site', '@' . $twitter_id);
	}
	$page->add_meta ('twitter:title', $e->title);
	$page->add_meta ('twitter:description', $desc);

	if ($e->thumbnail !== '') {
		$page->add_meta (
			'twitter:image',
			($this->is_https () ? 'https' : 'http') . '://'. Appconf::admin ('Site Settings', 'site_domain') . $e->thumbnail
		);
	}

    echo $tpl->render ('events/event', $e->orig ());
} else {
	if (! $this->internal) {
		$page->id = 'events';
		$page->title = __ ($appconf['Events']['title']);
		$page->layout = $appconf['Events']['layout'];
	}

	$this->run ('admin/util/minimal-grid');
	$page->add_style ('/apps/events/css/events.css');
	
	if (isset ($this->params[0]) && $this->params[0] === 'category') {
		if (! isset ($this->params[1])) {
			$this->redirect ('/events');
		}
		$category = $this->params[1];
	} else {
		$category = false;
	}
	
	$data = array (
		'limit' => 20,
		'details' => 'yes',
		'events' => array (),
		'category' => $category,
		'categories' => events\Category::query ()->order ('name', 'asc')->fetch_assoc ('id', 'name')
	);

	$start = gmdate ('Y-m-d');

	if ($category) {
		$data['events'] = Event::query ()
			->where (function ($q) {
				$q->where ('access', 'public');
				if (User::require_login ()) {
					$q->or_where ('access', 'member');
				}
			})
			->where ('category', $category)
			->where ('start_date >= "' . $start . '"')
			->order ('start_date', 'asc')
			->fetch_orig ($data['limit']);
	} else {
		$data['events'] = Event::query ()
			->where (function ($q) {
				$q->where ('access', 'public');
				if (User::require_login ()) {
					$q->or_where ('access', 'member');
				}
			})
			->where ('start_date >= "' . $start . '"')
			->order ('start_date', 'asc')
			->fetch_orig ($data['limit']);
	}
	
	$data['events'] = is_array ($data['events']) ? $data['events'] : array ();

	foreach ($data['events'] as $key => $event) {
		$data['events'][$key]->date = $event->start_date . ' ' . $event->starts;
	}
	
	echo $tpl->render ('events/index', $data);
}
