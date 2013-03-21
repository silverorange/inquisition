create or replace view InquisitionResponseTotalsView as
	select InquisitionResponse.id as response, sum(tracked_time) as total_time,
			count(1) as total_responses,
			count(
				case when answer_provided = false
					then null
					else true
				end
			) as total_answer_provided
		from InquisitionResponse
			left outer join InquisitionResponseValue
				on InquisitionResponseValue.response = InquisitionResponse.id
			inner join Inquisition
				on InquisitionResponse.inquisition = Inquisition.id
		group by InquisitionResponse.id;
