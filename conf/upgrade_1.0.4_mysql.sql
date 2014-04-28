alter table #prefix#event add column price float not null default 0.0;
alter table #prefix#event add column available int not null default 0;

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