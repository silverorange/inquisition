create table InquisitionInquisitionQuestionBinding (
	id serial,
	inquisition integer not null references Inquisition(id) on delete cascade,
	question integer not null references InquisitionQuestion(id) on delete cascade,
	displayorder integer not null default 0,

	primary key (id)
);

create index InquisitionInquisitionQuestionBinding_inquisition_index on
	InquisitionInquisitionQuestionBinding(inquisition);

create index InquisitionInquisitionQuestionBinding_question_index on
	InquisitionInquisitionQuestionBinding(question);
