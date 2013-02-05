create table InquisitionResponseValue (
	id serial,
	response integer not null references InquisitionResponse(id) on delete cascade,
	question_option integer null references InquisitionQuestionOption(id) on delete cascade,
	question_binding integer not null references InquisitionInquisitionQuestionBinding(id) on delete cascade,
	numeric_value          integer,
	text_value             text,
	primary key (id)
);

create index InquisitionResponseValue_response_index on
	InquisitionResponseValue(response);

create index InquisitionResponseValue_question_option_index on
	InquisitionResponseValue(question_option);
