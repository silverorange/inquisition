create table InquisitionResponse (
	id serial,
	inquisition integer not null references Inquisition(id) on delete cascade,
	createdate timestamp,
	primary key (id)
);

create index InquisitionResponse_inquisition_index on InquisitionResponse(inquisition);
