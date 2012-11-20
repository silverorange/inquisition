create table InquisitionQuestionImageBinding (
	question integer not null references InquisitionQuestion(id) on delete cascade,
	image integer not null references Image(id) on delete cascade,
	displayorder integer not null default 0,
	primary key (question, image)
);

CREATE INDEX InquisitionQuestionImageBinding_question_index ON InquisitionQuestionImageBinding(question);
CREATE INDEX InquisitionQuestionImageBinding_image_index ON InquisitionQuestionImageBinding(image);
