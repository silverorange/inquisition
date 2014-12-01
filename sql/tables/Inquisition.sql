create table Inquisition (
	id serial,
	title varchar(255),
	createdate timestamp not null,
	enabled boolean not null default true,
	primary key (id)
);

