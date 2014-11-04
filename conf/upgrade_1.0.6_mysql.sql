alter table #prefix#event add column category int not null default 0;
alter table #prefix#event add column thumbnail char(128) not null default '';

create index #prefix#event_category_date on #prefix#event (category, start_date, starts, end_date);

create table #prefix#event_registration (
	id int not null auto_increment primary key,
	event_id int not null,
	user_id int not null,
	payment_id int not null,
	ts datetime not null,
	status tinyint not null,
	expires datetime not null,
	num_attendees int not null,
	attendees text not null,
	company char(72) not null,
	index (event_id, num_attendees, status, expires),
	index (status, expires),
	index (event_id),
	index (user_id),
	index (ts)
);

create table #prefix#event_category (
	id int not null auto_increment primary key,
	name char(72) not null,
	index (name)
);
