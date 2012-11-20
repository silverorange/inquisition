create table InquisitionQuestionOptionImageBinding (
	question_option integer not null references InquisitionQuestionOption(id) on delete cascade,
	image integer not null references Image(id) on delete cascade,
	displayorder integer not null default 0,
	primary key (question_option, image)
);

CREATE INDEX InquisitionQuestionOptionImageBinding_question_option_index ON InquisitionQuestionOptionImageBinding(question_option);
CREATE INDEX InquisitionQuestionOptionImageBinding_image_index ON InquisitionQuestionOptionImageBinding(image);
