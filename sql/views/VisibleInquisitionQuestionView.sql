create or replace view VisibleInquisitionQuestionView as
	select InquisitionQuestionOption.question from InquisitionQuestionOption
	inner join InquisitionQuestion on
			InquisitionQuestionOption.question = InquisitionQuestion.id
	where InquisitionQuestion.enabled = true
	group by question;
