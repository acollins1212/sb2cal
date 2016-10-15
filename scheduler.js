function parseSchedule() {
	var fullSchedule = document.getElementById("textArea_id").value;
	var splitSchedule = fullSchedule.split("Actions");
	document.getElementById("time_disp").innerHTML = splitSchedule[0];

	var meeting_type_pattern = /[a-zA-Z]+/;
   	var start_time_pattern = /\d{1,2}:\d{2} [A|P]M/;
   	var end_time_pattern = /- (\d{1,2}:\d{2} [A|P]M)/;
   	var days_of_week_pattern = /([A|P]M)([MTWRFS]+)[A-Z]/;
   	var TEST = new Course(splitSchedule[0]);
}


meeting_type_pattern = /[a-zA-Z]+/;
start_time_pattern = /\d{1,2}:\d{2} [A|P]M/;
end_time_pattern = /- (\d{1,2}:\d{2} [A|P]M)/;
days_of_week_pattern = /([A|P]M)([MTWRFS]+)[A-Z]/;


var Course = function(class_string){
	name_pattern = /(^[A-Z]{3} \d{3}.{0,1}) (.{3}) - (.+)/;
    schedule_pattern = /'^.*[A|P]M .*/;
    time_pattern = /.*\d{1,2}:\d{2} [A|P]M/;
}


var Course_meeting = function(single_schedule){
	meeting_type_pattern = /[a-zA-Z]+/gm;
	start_time_pattern = /\d{1,2}:\d{2} [A|P]M/gm;
	end_time_pattern = /- (\d{1,2}:\d{2} [A|P]M)/gm;
	days_of_week_pattern = /([A|P]M)([MTWRFS]+)[A-Z]/gm;

	this.meeting_type = "";
	this.start_time = "";
	this.end_time = "";
	this.days_of_week = "";
	this.location = "";

	
}