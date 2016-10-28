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

; A callback to check for available discounts for the current user's
; membership type, specified as a percentage discount.
discount_callback = ""

; A callback to check whether an "invoice me" option should be available
; for events payments, allowing users to enter immediately and admins
; to receive a notice of registration that they will manually invoice for.
allow_invoice_callback = ""

; Whether to include this app in the list of pages; available to the Tools > Navigation tree.

include_in_nav = On

[Admin]

handler = events/admin
name = Events
install = events/install
upgrade = events/upgrade
version = 1.1.1-stable
sitemap = "Event::sitemap"
search = "Event::search"

; */ ?>
