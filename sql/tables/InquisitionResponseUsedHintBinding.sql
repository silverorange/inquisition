create table InquisitionResponseUsedHintBinding (
	response integer not null references InquisitionResponse(id) on delete cascade,
	question_hint integer not null references InquisitionQuestionHint(id) on delete cascade,
	primary key (response, question_hint)
);

CREATE INDEX InquisitionResponseUsedHintBinding_response_index ON InquisitionResponseUsedHintBinding(response);
CREATE INDEX InquisitionResponseUsedHintBinding_question_hint_index ON InquisitionResponseUsedHintBinding(question_hint);
