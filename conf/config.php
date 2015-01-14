; <?php /*

[Events]

; This is the title of your events calendar page (/events).

title = Events

; This is the layout to use for the events calendar.

layout = default

; This is the layout to use for the event details page.

event_layout = default

; Google Calendar ID. For more info, see:
; http://fullcalendar.io/docs/google_calendar/

gcal_id = ""

; Google Calendar API browser key. For more info, see:
; http://fullcalendar.io/docs/google_calendar/

gcal_browser_key = ""

; The payment handler for paid courses.
payment_handler = ""

; Whether to include this app in the list of pages; available to the Tools > Navigation tree.

include_in_nav = On

[Admin]

handler = events/admin
name = Events
install = events/install
upgrade = events/upgrade
version = 1.0.8-stable
sitemap = "Event::sitemap"
search = "Event::search"

; */ ?>
