/**
A.J. Collins
October 2016

This is translated from python.

Reworked on October 28 because the server-side 
	script does all of the parsing now. 

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

var Course = function(name_str, section, desc, finalDate, finalTime){
	//FIXME: GET SECTION IN HANDLE!!!

	this.name_str = name_str;
	this.section = section;
	this.description_str = desc;

	this.meeting_array = [];

	
	//FINAL EXAM PORTION
	this.finalExam_date = finalDate;
	//The date is in MM/DD/YYYY format. 
	var mmddyyyy = this.finalExam_date.split('/');
	var time = parseTime(finalTime);
	//Months are zero indexed, so I'm subtracting one inside constructor
	this.finalExam_date = new Date(mmddyyyy[2], mmddyyyy[0] - 1, mmddyyyy[1], time[0], time[1], 0, 0 );
	this.finalExam_date.setHours(this.finalExam_date.getHours() - 7); //-7 for timezone offset
	this.finalExam_end = new Date(this.finalExam_date);
	this.finalExam_end.setHours(this.finalExam_date.getHours() + 2); //finals last for two hours
	
	this.finalExam_date = this.finalExam_date.toISOString();
	this.finalExam_date = this.finalExam_date.replace('.000Z', '-07:00');
	this.finalExam_end = this.finalExam_end.toISOString();
	this.finalExam_end = this.finalExam_end.replace('.000Z', '-07:00');


	this.addMeeting = function(type, start, end, loc, dow) {
		var m = new Course_meeting(type, start, end, loc, dow);
		this.meeting_array.push(m);
	} //this.addMeeting()


	this.getFinalJSON = function() {



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

    	if(meeting_number >= this.meeting_array.length) {
    		console.log("Out of Bounds error");
    	}

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

		var end_datetime = new Date(FIRST_DAY);
		end_datetime.setDate(FIRST_DAY.getDate() + addition);
		end_datetime.setHours(course_event.end_time[0] - 7); //-7 accounts for toISOString() adding 7 
		end_datetime.setMinutes(course_event.end_time[1]);
		end_datetime = end_datetime.toISOString();
		end_datetime = end_datetime.replace('.000Z', '-07:00');

		
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


var Course_meeting = function(type, start, end, loc, dow){
	
	this.meeting_type = type;

	this.start_time = start;
	this.start_time = parseTime(this.start_time);

	this.end_time = end;
	this.end_time = parseTime(this.end_time);

	this.location = loc;
	this.days_of_week = dow;
	
} //Course_meeting()