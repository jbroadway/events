create table #prefix#event (
	id int not null auto_increment primary key,
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
	phone char(48) not null,
	index (start_date, starts, end_date)
);
