create or replace view InquisitionResponseGradeView as
	select InquisitionResponse.inquisition,
		InquisitionResponse.account,
		sum(
			case when InquisitionQuestion.correct_option =
				InquisitionResponseValue.question_option then 1
			else 0
			end
		)::float /
		count(InquisitionResponseValue.id)::float as grade
	from InquisitionResponse
		inner join InquisitionResponseValue on
			InquisitionResponseValue.response = InquisitionResponse.id
		inner join InquisitionInquisitionQuestionBinding on
			InquisitionInquisitionQuestionBinding.id =
				InquisitionResponseValue.question_binding
		inner join InquisitionQuestion on
			InquisitionQuestion.id =
				InquisitionInquisitionQuestionBinding.question
	where InquisitionResponse.complete_date is not null
		and InquisitionResponse.reset_date is null
		and InquisitionQuestion.correct_option is not null
	group by InquisitionResponse.inquisition, InquisitionResponse.account;

