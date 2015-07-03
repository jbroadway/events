; <?php /*

[events/calendar]

label = "Event Calendar"

category[label] = Category
category[type] = select
category[initial] = ""
category[require] = "apps/events/lib/Functions.php"
category[callback] = "events_categories"

[events/sidebar]

label = "Event Listings"

category[label] = Category
category[type] = select
category[initial] = ""
category[require] = "apps/events/lib/Functions.php"
category[callback] = "events_categories"

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

[events/past]

label = "Past Events"

category[label] = Category
category[type] = select
category[initial] = ""
category[require] = "apps/events/lib/Functions.php"
category[callback] = "events_categories"

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
