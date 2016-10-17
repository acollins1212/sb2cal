function parseTime() {

	//time_string is expected as (X)X:XX AM/PM
	var textAreaVal = document.getElementById("textArea_id").value;
	var timeStr = textAreaVal;
	var splitStr = timeStr.split(' ');
	var hourMinute = splitStr[0];
	var dayHalf = splitStr[1];

	var time = hourMinute.split(':');
	var hour = parseInt(time[0]);
	var minute = parseInt(time[1]);

	//this changes the hour to 24-hour time
	if(time[0] != "12" && dayHalf == "PM"){
		hour += 12;
	}

	return time;
}

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

var Course = function(class_string){
	name_pattern = /(^[A-Z]{3} \d{3}.{0,1}) (.{3}) - (.+)/m;
    schedule_pattern = /^.*[A|P]M .*/gm;
    exam_pattern = /.* (\d{1,2}\/\d{1,2}\/\d{4}) \d{1,2}:\d{2} [A|P]M/m;

    var reg_obj_name = class_string.match(name_pattern);

    this.name_str = reg_obj_name[1];
    this.section = reg_obj_name[2];
    this.description_str = reg_obj_name[3];

    this.schedule_array = class_string.match(schedule_pattern);
    this.meeting_array = [];
    
    
    for(i = 0; i < this.schedule_array.length; i++){
    	this.meeting_array.push(new Course_meeting(this.schedule_array[i]));
    }

    //console.log(this.meeting_array);

    this.finalExam_str = class_string.match(exam_pattern)[0];
    this.finalExam_date = class_string.match(exam_pattern)[1];
    this.finalExam_date = this.finalExam_date.split('/');

    //FIXME: create datetimes from the final exam
}


var Course_meeting = function(single_schedule){
	meeting_type_pattern = /[a-zA-Z]+/;
	start_time_pattern = /\d{1,2}:\d{2} [A|P]M/;
	end_time_pattern = /- (\d{1,2}:\d{2} [A|P]M)/;
	days_of_week_pattern = /([A|P]M)([MTWRFS]+)[A-Z]/;
	location_pattern = /[A|P]M[MTWRFS]+([A-Z].*)/;

	this.meeting_type = "";
	this.start_time = "";
	this.end_time = "";
	this.days_of_week = "";
	this.location = "";

	//taking meeting_type out of schedule passed in
	if (this.meeting_type = single_schedule.match(meeting_type_pattern)){
	} // do nothing
	else {
		console.log("meeting_type null");
	}

	//taking start_time out of schedule passed in
	if (this.start_time = single_schedule.match(start_time_pattern)){
	} //do nothing
	else {
		console.log("start_time null");
	}

	//Taking end_time out of schedule passed in
	if (this.end_time = single_schedule.match(end_time_pattern)) {
	} //do nothing
	else {
		console.log("end_time null");
	}

	//Taking days_of_week out of schedule passed in 
	if (this.days_of_week = single_schedule.match(days_of_week_pattern)) {

	    this.days_of_week = this.days_of_week[1];
	    var dow_length = this.days_of_week.length-1;
		//RFC 5545 takes recurrence dates given with two letter abbreviations
		this.days_of_week = this.days_of_week.replace('M', 'MO,')
        this.days_of_week = this.days_of_week.replace('T', 'TU,')
        this.days_of_week = this.days_of_week.replace('W', 'WE,')
        this.days_of_week = this.days_of_week.replace('R', 'TH,')
        this.days_of_week = this.days_of_week.replace('F', 'FR,')
        this.days_of_week = this.days_of_week.substring(0,dow_length);
	}
	else {
		console.log("dow null");
	}

	//taking location out of schedule passed in 
	if (this.location = single_schedule.match(location_pattern)) {
		//do nothing
	}
	else {
		console.log("location null");
	}
	
} //Course_meeting()