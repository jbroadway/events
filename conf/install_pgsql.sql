create table #prefix#event (
	id serial not null primary key,
	title character varying(72) not null,
	start_date date not null,
	end_date date not null,
	starts timestamp not null,
	ends timestamp not null,
	details text not null,
	address character varying(48) not null,
	city character varying(48) not null,
	contact character varying(48) not null,
	email character varying(48) not null,
	phone character varying(48) not null,
	price float not null default 0.0,
	available integer not null default 0,
	category integer not null default 0,
	thumbnail character varying(128) not null default '',
	venue character varying(48) not null default '',
	"access" character(12) not null default 'public'
);

create index #prefix#event_date on #prefix#event ("access", start_date, starts, end_date);
create index #prefix#event_category_date on #prefix#event ("access", category, start_date, starts, end_date);

create table #prefix#event_registration (
	id serial not null primary key,
	event_id integer not null,
	user_id integer not null,
	payment_id integer not null,
	ts timestamp not null,
	status integer not null,
	expires timestamp not null,
	num_attendees integer not null,
	attendees text not null,
	company character varying(72) not null
);

create index #prefix#event_registration_attendees on #prefix#event_registration (event_id, num_attendees, status, expires);
create index #prefix#event_registration_status on #prefix#event_registration (status, expires);
create index #prefix#event_registration_event_id on #prefix#event_registration (event_id);
create index #prefix#event_registration_user_id on #prefix#event_registration (user_id);
create index #prefix#event_registration_ts on #prefix#event_registration (ts);

create table #prefix#event_category (
	id serial not null primary key,
	name character varying(72) not null
);

create index #prefix#event_category_name on #prefix#event_category (name);