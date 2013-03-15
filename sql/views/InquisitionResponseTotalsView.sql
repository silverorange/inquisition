create or replace view InquisitionResponseTotalsView as
	select InquisitionResponse.id as response, sum(tracked_time) as total_time,
			count(1) as total_responses,
			count(answer_provided = true) as total_answer_provided
		from InquisitionResponse
			left outer join InquisitionResponseValue
				on InquisitionResponseValue.response = InquisitionResponse.id
			inner join Inquisition
				on InquisitionResponse.inquisition = Inquisition.id
		where Inquisition.account is not null
		group by InquisitionResponse.id;
