create table InquisitionQuestion (
	id serial,
	bodytext text,
	question_type integer not null,
	displayorder integer not null default 0,
	required boolean not null default true,
	enabled boolean not null default true,
	primary key (id)
);

alter table InquisitionQuestion add correct_option integer references InquisitionQuestionOption(id) on delete set null;

