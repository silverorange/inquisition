create or replace view VisibleInquisitionQuestionView as
	select question from InquisitionQuestionOption
	group by question;
