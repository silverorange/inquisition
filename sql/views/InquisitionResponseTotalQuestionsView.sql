create or replace view InquisitionResponseTotalQuestionsView as
	select InquisitionResponse.id as response, count(1) as total_questions
		from InquisitionResponse
			inner join Inquisition
				on InquisitionResponse.inquisition = Inquisition.id
			left outer join InquisitionInquisitionQuestionBinding
				on InquisitionInquisitionQuestionBinding.inquisition = Inquisition.id
		group by InquisitionResponse.id;
