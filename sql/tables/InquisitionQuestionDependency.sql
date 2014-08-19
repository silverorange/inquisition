create table InquisitionQuestionDependency
(
	question_binding integer not null references InquisitionInquisitionQuestionBinding(id) on delete cascade,
	question integer not null references InquisitionQuestion(id) on delete cascade,
	option integer not null references InquisitionQuestionOption(id) on delete cascade,

	dependent_question_binding integer not null references InquisitionInquisitionQuestionBinding(id) on delete cascade,
	dependent_question integer not null references InquisitionQuestion(id) on delete cascade
);

create index InquisitionQuestionDependency_dependent_question_binding_index on
	InquisitionQuestionDependency(dependent_question_binding);

create index InquisitionQuestionDependency_dependent_question_index on
	InquisitionQuestionDependency(dependent_question);
