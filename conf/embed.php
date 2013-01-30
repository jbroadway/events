; <?php /*

[events/index]

label = "Event Calendar"

[events/sidebar]

label = "Event Listings"

limit[label] = Limit
limit[type] = text
limit[initial] = 10
limit[not empty] = 1
limit[type] = numeric
limit[message] = Please enter a limit value.

details[label] = Show details
details[type] = select
details[initial] = "no"
details[require] = "apps/events/lib/Functions.php"
details[callback] = "events_yes_no"

; */ ?>