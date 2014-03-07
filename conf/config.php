; <?php

[Events]

; This is the title of your events calendar page (/events).

title = Events

; This is the layout to use for the events calendar.

layout = default

; This is the layout to use for the event details page.

event_layout = default

; Google Calendar link. Must be the XML feed of a public
; calendar. For more information about changing your
; calendar settings, see:
; http://arshaw.com/fullcalendar/docs/google_calendar/

gcal_link = ""

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
search = "Event::search"

; */ ?>