create table event (
	id integer primary key,
	title char(48) not null,
	start_date date not null,
	end_date date not null,
	starts time not null,
	ends time not null,
	details text not null,
	address char(48) not null,
	city char(48) not null,
	contact char(48) not null,
	email char(48) not null,
	phone char(48) not null
);

create index event_date on event (start_date, starts, end_date);
