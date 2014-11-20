alter table #prefix#event add column access enum('public','member','private') not null default 'public';
