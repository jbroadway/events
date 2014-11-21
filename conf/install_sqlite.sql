create table #prefix#event (
	id integer primary key,
	title char(72) not null,
	start_date date not null,
	end_date date not null,
	starts time not null,
	ends time not null,
	details text not null,
	address char(48) not null,
	city char(48) not null,
	contact char(48) not null,
	email char(48) not null,
	phone char(48) not null,
	price float not null default 0.0,
	available int not null default 0,
	category int not null default 0,
	thumbnail char(128) not null default '',
	venue char(48) not null default '',
	access char(12) not null default 'public'
);

create index #prefix#event_date on #prefix#event (access, start_date, starts, end_date);
create index #prefix#event_category_date on #prefix#event (access, category, start_date, starts, end_date);

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

create table #prefix#event_category (
	id integer primary key,
	name char(72) not null
);

create index #prefix#event_category_name on #prefix#event_category (name);
