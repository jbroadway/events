create table #prefix#event (
	id int not null auto_increment primary key,
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
	access enum('public','member','private') not null default 'public',
	index (access, start_date, starts, end_date),
	index (access, category, start_date, starts, end_date)
);

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
