function parseSchedule() {
	var fullSchedule = document.getElementById("textArea_id").value;
	var splitSchedule = fullSchedule.split("Actions");
	document.getElementById("time_disp").innerHTML = splitSchedule[0];

	var meeting_type_pattern = /[a-zA-Z]+/;
   	var start_time_pattern = /\d{1,2}:\d{2} [A|P]M/;
   	var end_time_pattern = /- (\d{1,2}:\d{2} [A|P]M)/;
   	var days_of_week_pattern = /([A|P]M)([MTWRFS]+)[A-Z]/;
}


meeting_type_pattern = /[a-zA-Z]+/;
start_time_pattern = /\d{1,2}:\d{2} [A|P]M/;
end_time_pattern = /- (\d{1,2}:\d{2} [A|P]M)/;
days_of_week_pattern = /([A|P]M)([MTWRFS]+)[A-Z]/;

var Course_meeting = function(singleSchedule){
	this.meeting_type_pattern = /[a-zA-Z]+/gm;
	this.start_time_pattern = /\d{1,2}:\d{2} [A|P]M/gm;
	this.end_time_pattern = /- (\d{1,2}:\d{2} [A|P]M)/gm;
	this.days_of_week_pattern = /([A|P]M)([MTWRFS]+)[A-Z]/gm;

	this.meeting_type = "";
	this.start_time = "";
	this.end_time = "";
	this.days_of_week = "";
	this.location = "";
	






}