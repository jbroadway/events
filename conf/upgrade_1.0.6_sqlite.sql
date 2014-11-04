alter table #prefix#event add column category int not null default 0;
alter table #prefix#event add column thumbnail char(128) not null default '';
alter table #prefix#event add column venue char(48) not null default '';

create index #prefix#event_category_date on #prefix#event (category, start_date, starts, end_date);

create table #prefix#event_category (
	id integer primary key,
	name char(72) not null
);

create index #prefix#event_category_name on #prefix#event_category (name);
