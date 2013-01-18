create table InquisitionInquisitionQuestionBinding (
	inquisition integer not null references Inquisition(id) on delete cascade,
	question integer not null references InquisitionQuestion(id) on delete cascade,
	displayorder integer not null default 0,

	primary key (inquisition, question)
);