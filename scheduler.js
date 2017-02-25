/**
A.J. Collins

CONTAINS:
	Parsing functions
	Course and Course_Meeting class definitions


**/


var  FIRST_DAY = new Date(2017, 0, 9, 1, 0, 0, 0);
var  LAST_DAY = new Date(2017, 2, 18, 16, 59, 0, 0);
var  DAYS_OFF;

//I plan to form validate with Angular eventually. This is a crappy fix
function fixID() {
			var idInput = document.forms["form"]["CALENDAR_ID"].value;
			var invalidCharacters = /[^@.\d\w_]|Calendar ID:/gi; //the only characters allowed, in a complement set
			var scrubbedInput = idInput.replace(invalidCharacters, "");
			document.forms["form"]["CALENDAR_ID"].value = scrubbedInput;
			if (scrubbedInput.length > 100) {
				return false;
			}
			return true;
}

function parseTime(time_str) {

	//time_str is expected as (X)X:XX AM/PM
	
	var split_str = time_str.split(' ');
	var hour_minute = split_str[0];
	var day_half = split_str[1];

	var time = hour_minute.split(':');
	time[0] = parseInt(time[0]);
	time[1] = parseInt(time[1]);

	//this changes the hour to 24-hour time
	if(time[0] != "12" && day_half == "PM"){
		time[0] += 12;
	}
	
	return time;
} //parseTime()

function parseSchedule() {
	var full_schedule = document.getElementById("textArea_id").value;

	var course_name_pattern = /[A-Z]{3} [0-9]{3}[A-Z]{0,1} [A-Z0-9]{3} - .*/gm;
	var course_name_array = full_schedule.match(course_name_pattern);

	var unique_delimiter = "!@#$%^&*";
	var num_courses = course_name_array.length;
	for(var i = 0; i < num_courses; i++){
		var current_course = course_name_array[i];
		//Adding random punctuation as future delimiter to split up full schedule
		full_schedule = full_schedule.replace(current_course, unique_delimiter.concat(current_course));
	}

	var split_schedule = full_schedule.split(unique_delimiter);
	split_schedule.shift();

   	return split_schedule;
} //parseSchedule()

var Course = function(class_string){

	//WEEKLY SCHEDULE PORTION
	var name_pattern = /(^[A-Z]{3} \d{3}[A-Z]{0,2}) (.{3}) - (.+)/m;
	var schedule_pattern = /^[A-Z][a-zA-Z\/ ]*[\r\n]*[0-9]{1,2}:[0-9]{2} [A|P]M - [0-9]{1,2}:[0-9]{2} [A|P]M[\r\n]*[A-Z]*[\r\n]*.*$/gm;
	var exam_pattern = /.* (\d{1,2}\/\d{1,2}\/\d{4}) (\d{1,2}:\d{2} [A|P]M)/m;

	var reg_obj_name = class_string.match(name_pattern);

	this.name_str = reg_obj_name[1];
	this.section = reg_obj_name[2];
	this.description_str = reg_obj_name[3];

	this.schedule_array = class_string.match(schedule_pattern);

	this.meeting_array = [];


	for(i = 0; i < this.schedule_array.length; i++){
		this.meeting_array.push(new Course_meeting(this.schedule_array[i]));
	}

	// FIXME: Deal with lack of final exam for certain courses
	//FINAL EXAM PORTION
	this.finalExam_str = class_string.match(exam_pattern);
	//The date is in MM/DD/YYYY format. 
	var mmddyyyy = this.finalExam_str[1].split('/');
	var time = parseTime(this.finalExam_str[2]);
	//Months are zero indexed, so I'm subtracting one inside constructor
	this.finalExam_date = new Date(mmddyyyy[2], mmddyyyy[0] - 1, mmddyyyy[1], time[0], time[1], 0, 0 );
	this.finalExam_date.setHours(this.finalExam_date.getHours() - 7); //-7 for timezone offset
	this.finalExam_end = new Date(this.finalExam_date);
	this.finalExam_end.setHours(this.finalExam_date.getHours() + 2); //finals last for two hours
	
	this.finalExam_date = this.finalExam_date.toISOString();
	this.finalExam_date = this.finalExam_date.replace('.000Z', '-07:00');
	this.finalExam_end = this.finalExam_end.toISOString();
	this.finalExam_end = this.finalExam_end.replace('.000Z', '-07:00');



	this.getFinalJSON = function() {
		console.log("Final Exam date: " + this.finalExam_date);



		finalExam = {
			'summary': this.name_str+ " Final",
			'description': "Good luck!",
			'start': {
				'dateTime': this.finalExam_date,
				'timeZone': 'America/Los_Angeles'
			},
			'end': {
				'dateTime': this.finalExam_end,
				'timeZone': 'America/Los_Angeles'
			},
    	} //finalExam

    	return finalExam;
    } //this.getFinalJSON()


    this.getEventJSON = function(meeting_number) {

    	var course_event = this.meeting_array[meeting_number];
    	var desc = this.description_str + "\nSection: " + this.section;

    	var last_day_iso = LAST_DAY.toISOString().replace(/[:\-.]/g,'');
    	last_day_iso = last_day_iso.replace(/00000Z/, '00Z');


		//RECURRENCE STRING
		var frequency_string = 'FREQ=WEEKLY';
		var until_string = 'UNTIL='+last_day_iso;
		var byday_string = "BYDAY="+course_event.days_of_week;
		var recurrence_string = 'RRULE:WKST=SU;' + frequency_string + ';'
		+ until_string + ';' +byday_string;
		var exceptionString = "";

		console.log(until_string);

		//To give the proper start date of a class
		var addition = 0;
		if(course_event.days_of_week.search('MO') != -1){
			addition = 0;

		}
		else if(course_event.days_of_week.search('TU') != -1){
			addition = 1;
		}
		else if(course_event.days_of_week.search('WE') != -1){
			addition = 2;
		}
		else if(course_event.days_of_week.search('TH') != -1){
			addition = 3;
		}
		else if(course_event.days_of_week.search('FR') != -1){
			addition = 4;
		}

		var start_datetime = new Date(FIRST_DAY);
		start_datetime.setDate(FIRST_DAY.getDate() + addition);
		start_datetime.setHours(course_event.start_time[0] - 7); //-7 accounts for toISOString() adding 7
		start_datetime.setMinutes(course_event.start_time[1]);
		start_datetime = start_datetime.toISOString();
		start_datetime = start_datetime.replace('.000Z', '-07:00');

		console.log(start_datetime);

		var end_datetime = new Date(FIRST_DAY);
		end_datetime.setDate(FIRST_DAY.getDate() + addition);
		end_datetime.setHours(course_event.end_time[0] - 7); //-7 accounts for toISOString() adding 7 
		end_datetime.setMinutes(course_event.end_time[1]);
		end_datetime = end_datetime.toISOString();
		end_datetime = end_datetime.replace('.000Z', '-07:00');

		console.log(end_datetime);
		
		//I'M REALLY NOT UNDERSTANDING TIMEZONING. HOPEFULLY ILL UNDERSTAND IT L8R
		//I JUST DISCOVERED THAT THE GCALENDAR IS SET TO GMT (due to daylight savings)

		var summary_string = this.name_str + " " + course_event.meeting_type;
		
		var event = {
			"summary": summary_string,
			"location": course_event.location,
			"description": desc,
			"start": {
				"dateTime": start_datetime,
				"timeZone": "America/Los_Angeles"
			}, 
			"end": {
				"dateTime": end_datetime,
				"timeZone": "America/Los_Angeles"
			},
			"recurrence": [
				recurrence_string
			]

		}; //event
		return event;

    } //this.getEventJSON()



} //Course


var Course_meeting = function(single_schedule){

	fullPattern = /([a-zA-Z/]+)[\r\n]*(\d{1,2}:\d{2} [A|P]M) - (\d{1,2}:\d{2} [A|P]M)[\r\n]*([MTWRFS]+)[\r\n]*([A-Z].*)/m;
	if (scheduleString = single_schedule.match(fullPattern)){
		//do nothing
	}
	else {
		console.log("scheduleString not found!");
	}

	this.meeting_type = "";
	this.start_time = "";
	this.end_time = "";
	this.days_of_week = "";
	this.location = "";

	this.meeting_type = scheduleString[1];
	this.start_time = scheduleString[2];
	this.end_time = scheduleString[3];
	this.days_of_week = scheduleString[4];
	this.location = scheduleString[5];

	//RFC 5545 takes recurrence dates as two letter abbreviations
	this.days_of_week = this.days_of_week.replace('M', 'MO,');
	this.days_of_week = this.days_of_week.replace('T', 'TU,');
	this.days_of_week = this.days_of_week.replace('W', 'WE,');
	this.days_of_week = this.days_of_week.replace('R', 'TH,');
	this.days_of_week = this.days_of_week.replace('F', 'FR,');
	var dow_length = this.days_of_week.length-1;
	this.days_of_week = this.days_of_week.substring(0,dow_length);
	
	
} //Course_meeting()