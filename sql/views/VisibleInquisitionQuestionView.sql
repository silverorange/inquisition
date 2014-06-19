create or replace view VisibleInquisitionQuestionView as
	select id as question from InquisitionQuestion
	where InquisitionQuestion.enabled = true and (
		-- Question is always visible if it InquisitionQuestion::TYPE_TEXT (4),
		-- or if it has related options
		InquisitionQuestion.question_type = 4 or InquisitionQuestion.id in (
			select InquisitionQuestionOption.question from InquisitionQuestionOption
		)
	);
