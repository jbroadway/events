alter table #prefix#event add column price float not null default 0.0;
alter table #prefix#event add column available int not null default 0;

create table #prefix#event_registration (
	id integer primary key,
	event_id int not null,
	user_id int not null,
	payment_id int not null,
	ts datetime not null,
	status tinyint not null,
	expires datetime not null,
	num_attendees int not null,
	attendees text not null,
	company char(72) not null
);

create index #prefix#event_registration_attendees on #prefix#event_registration (event_id, num_attendees, status, expires);
create index #prefix#event_registration_status on #prefix#event_registration (status, expires);
create index #prefix#event_registration_event_id on #prefix#event_registration (event_id);
create index #prefix#event_registration_user_id on #prefix#event_registration (user_id);
create index #prefix#event_registration_ts on #prefix#event_registration (ts);
