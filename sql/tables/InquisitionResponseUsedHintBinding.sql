create table InquisitionResponseUsedHintBinding (
	response integer not null references InquisitionResponse(id) on delete cascade,
	question_hint integer not null references InquisitionQuestionHint(id) on delete cascade,
	question_binding integer not null references InquisitionInquisitionQuestionBinding(id) on delete cascade,
	createdate timestamp not null default LOCALTIMESTAMP,
	primary key (response, question_hint, question_binding)
);

CREATE INDEX InquisitionResponseUsedHintBinding_response_index ON InquisitionResponseUsedHintBinding(response);
CREATE INDEX InquisitionResponseUsedHintBinding_question_hint_index ON InquisitionResponseUsedHintBinding(question_hint);
