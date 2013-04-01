; <?php

[Events]

; This is the title of your events calendar page (/events).

title = Events

; This is the layout to use for the events calendar.

layout = default

; This is the layout to use for the event details page.

event_layout = default

; Whether to include this app in the list of pages
; available to the Tools > Navigation tree.

include_in_nav = On

[Admin]

handler = events/admin
name = Events
install = events/install
upgrade = events/upgrade
version = 0.9-beta
sitemap = "Event::sitemap"

; */ ?>