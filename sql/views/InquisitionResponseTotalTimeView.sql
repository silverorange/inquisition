create or replace view InquisitionResponseTotalTimeView as
	select InquisitionResponse.id as response, sum(tracked_time) as total_time
		from InquisitionResponse
			left outer join InquisitionResponseValue
				on InquisitionResponseValue.response = InquisitionResponse.id
			inner join Inquisition
				on InquisitionResponse.inquisition = Inquisition.id
		where Inquisition.account is not null
		group by InquisitionResponse.id;
