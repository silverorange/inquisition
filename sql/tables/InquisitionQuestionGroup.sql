create table InquisitionQuestionGroup (
	id serial,
	title varchar(255),
	bodytext text,
	primary key (id)
);

alter table InquisitionQuestion add question_group integer references InquisitionQuestionGroup(id) on delete set null;

