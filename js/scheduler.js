/**
A.J. Collins
October 2016

This is translated from python. 

**/


var  FIRST_DAY = new Date(2017, 0, 9, 1, 0, 0, 0);
var  LAST_DAY = new Date(2017, 2, 18, 16, 59, 0, 0);

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

	//document.getElementById("time_disp").innerHTML = split_schedule[0];

   	//var TEST = new Course(split_schedule[0]);
   	//TEST.getEventJSON(0);

   	return split_schedule;
} //parseSchedule()

var Course = function(class_string){

	//WEEKLY SCHEDULE PORTION
	var name_pattern = /(^[A-Z]{3} \d{3}[A-Z]{0,2}) (.{3}) - (.+)/m;
	var schedule_pattern = /^.*[A|P]M .*/gm;
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
    		//FEATURE IDEA: ask user for reminders they want set
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

			/*
			"reminders": {
				"useDefault": false;
				'overrides': [
				{'method': 'email', 'minutes': 24 * 60},
				{'method': 'popup', 'minutes': 10}
				]

				//FEATURE IDEA: Ask user whether they want reminders
			}
			*/

			//FEATURE IDEA: Ask user what color they want for the calendar
		}; //event
		return event;

    } //this.getEventJSON()



} //Course


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
	if (this.meeting_type = single_schedule.match(meeting_type_pattern)[0]){
	} // do nothing
	else {
		console.log("meeting_type null");
	}


	//taking start_time out of schedule passed in
	if (this.start_time = single_schedule.match(start_time_pattern)[0]){
		this.start_time = parseTime(this.start_time);
	} //do nothing
	else {
		console.log("start_time null");
	}
	//Taking end_time out of schedule passed in
	if (this.end_time = single_schedule.match(end_time_pattern)[1]) {
		this.end_time = parseTime(this.end_time);
	} //do nothing
	else {
		console.log("end_time null");
	}

	//Taking days_of_week out of schedule passed in 
	if (this.days_of_week = single_schedule.match(days_of_week_pattern)[2]) {

		//RFC 5545 takes recurrence dates given with two letter abbreviations
		this.days_of_week = this.days_of_week.replace('M', 'MO,');
		this.days_of_week = this.days_of_week.replace('T', 'TU,');
		this.days_of_week = this.days_of_week.replace('W', 'WE,');
		this.days_of_week = this.days_of_week.replace('R', 'TH,');
		this.days_of_week = this.days_of_week.replace('F', 'FR,');
		var dow_length = this.days_of_week.length-1;

		this.days_of_week = this.days_of_week.substring(0,dow_length);
	}
	else {
		console.log("dow null");
	}

	//taking location out of schedule passed in 
	if (this.location = single_schedule.match(location_pattern)[1]) {
		//do nothing
	}
	else {
		console.log("location null");
	}
	
} //Course_meeting()