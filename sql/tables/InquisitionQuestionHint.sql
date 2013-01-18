create table InquisitionQuestionHint (
	id serial,
	question integer not null references InquisitionQuestion(id) on delete cascade,
	bodytext text,
	displayorder integer not null default 0,
	primary key(id)
);