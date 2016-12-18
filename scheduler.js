/**
A.J. Collins
October 2016

This is translated from python code I wrote.

Reworked on October 28 because the server-side 
    script does all of the parsing from the user input.
This packages up that input and calls the REST api 

**/


var  FIRST_DAY = new Date(2017, 0, 9, 1, 0, 0, 0);
var  LAST_DAY = new Date(2017, 2, 18, 16, 59, 0, 0);

function parseTime(time_str) {

    //time_str is expected as (X)X:XX AM/PM
    
    var splitStr = time_str.split(' ');
    var hourAndMinute = splitStr[0];
    var whichHalfOfDay = splitStr[1];

    var time = hourAndMinute.split(':');
    time[0] = parseInt(time[0]);
    time[1] = parseInt(time[1]);

    //this changes the hour to 24-hour time
    if(time[0] != "12" && whichHalfOfDay == "PM"){
        time[0] += 12;
    }
    
    return time;
} //parseTime()

function parseSchedule() {
    var fullSchedule = document.getElementById("textArea_id").value;

    var courseNamePattern = /[A-Z]{3} [0-9]{3}[A-Z]{0,1} [A-Z0-9]{3} - .*/gm;
    var courseNameArray = fullSchedule.match(courseNamePattern);

    var uniqueDelim = "!@#$%^&*";
    var numCourses = courseNameArray.length;
    for(var i = 0; i < numCourses; i++){
        var currentCourse = courseNameArray[i];
        //Inserting random punctuation as delimiter to split up full schedule
        fullSchedule = fullSchedule.replace(currentCourse, 
                                            uniqueDelim.concat(currentCourse));
    }

    var splitSchedule = fullSchedule.split(uniqueDelim);
    splitSchedule.shift(); //removes the first element, which is nothing


       return splitSchedule;
} //parseSchedule()

var Course = function(nameStr, section, desc, finalDate, finalTime){

    this.nameStr = nameStr;
    this.section = section;
    this.descriptionStr = desc;
    this.meetingArray = [];

    //FINAL EXAM PORTION
    this.finalExam_start = finalDate;
    //The date is in MM/DD/YYYY format. 
    var mmddyyyy = this.finalExam_start.split('/');
    var time = parseTime(finalTime);
    //Months are zero indexed, so I'm subtracting one inside constructor
    this.finalExam_start = new Date(mmddyyyy[2], mmddyyyy[0] - 1, mmddyyyy[1], 
                                    time[0], time[1], 0, 0 );
    //Subtract 7 for timezone offset
    this.finalExam_start.setHours(this.finalExam_start.getHours() - 7); 
    this.finalExam_end = new Date(this.finalExam_start);
    //finals last for two hours
    this.finalExam_end.setHours(this.finalExam_start.getHours() + 2); 
    
    this.finalExam_start = this.finalExam_start.toISOString();
    this.finalExam_start = this.finalExam_start.replace('.000Z', '-07:00');
    this.finalExam_end = this.finalExam_end.toISOString();
    this.finalExam_end = this.finalExam_end.replace('.000Z', '-07:00');


    this.addMeeting = function(type, start, end, loc, dow) {
        var m = new Course_meeting(type, start, end, loc, dow);
        this.meetingArray.push(m);
    } //this.addMeeting()


    this.getFinalJSON = function() {



        finalExam = {
            'summary': this.nameStr + " Final",
            'description': "Good luck!",
            'start': {
                'dateTime': this.finalExam_start,
                'timeZone': 'America/Los_Angeles'
            },
            'end': {
                'dateTime': this.finalExam_end,
                'timeZone': 'America/Los_Angeles'
            },
        } //finalExam

        return finalExam;
    } //this.getFinalJSON()


    this.getEventJSON = function(meetingNum) {

        var courseEvent = this.meetingArray[meetingNum];
        var desc = this.descriptionStr + "\nSection: " + this.section;

        var lastDay_iso = LAST_DAY.toISOString().replace(/[:\-.]/g,'');
        lastDay_iso = lastDay_iso.replace(/00000Z/, '00Z');


        //RECURRENCE STRING
        var frequencyStr = 'FREQ=WEEKLY';
        var untilStr = 'UNTIL='+lastDay_iso;
        var byDayStr = "BYDAY="+courseEvent.daysOfWeek;
        var recurrenceStr = 'RRULE:WKST=SU;' + frequencyStr + ';'
        + untilStr + ';' +byDayStr;

        //To give the proper start date of a class
        var addition = 0;
        if(courseEvent.daysOfWeek.search('MO') != -1){
            addition = 0;

        }
        else if(courseEvent.daysOfWeek.search('TU') != -1){
            addition = 1;
        }
        else if(courseEvent.daysOfWeek.search('WE') != -1){
            addition = 2;
        }
        else if(courseEvent.daysOfWeek.search('TH') != -1){
            addition = 3;
        }
        else if(courseEvent.daysOfWeek.search('FR') != -1){
            addition = 4;
        }

        var startDateTime = new Date(FIRST_DAY);
        startDateTime.setDate(FIRST_DAY.getDate() + addition);

        startDateTime.setHours(courseEvent.startTime[0] - 7); 
        startDateTime.setMinutes(courseEvent.startTime[1]);
        startDateTime = startDateTime.toISOString();
        startDateTime = startDateTime.replace('.000Z', '-07:00');

        var endDateTime = new Date(FIRST_DAY);
        endDateTime.setDate(FIRST_DAY.getDate() + addition);
        //Subtracting 7 to account for toISOString() adding 7
        endDateTime.setHours(courseEvent.endTime[0] - 7); 
        endDateTime.setMinutes(courseEvent.endTime[1]);
        endDateTime = endDateTime.toISOString();
        endDateTime = endDateTime.replace('.000Z', '-07:00');

        //This was set to a -7 offset because I wrote it during Daylight Savings
        //I'm pretty relieved that it still works after DST. Maybe it's because 
        //    I set the timezone when I insert the event, and GCalendar fixes it

        var summaryStr = this.nameStr + " " + courseEvent.meetingType;
        
        var event = {
            "summary": summaryStr,
            "location": courseEvent.location,
            "description": desc,
            "start": {
                "dateTime": startDateTime,
                "timeZone": "America/Los_Angeles"
            }, 
            "end": {
                "dateTime": endDateTime,
                "timeZone": "America/Los_Angeles"
            },
            "recurrence": [
                recurrenceStr
            ]

        }; //event
        return event;

    } //this.getEventJSON()



} //Course


var Course_meeting = function(type, start, end, loc, dow){
    
    this.meetingType = type;

    this.startTime = start;
    this.startTime = parseTime(this.startTime);

    this.endTime = end;
    this.endTime = parseTime(this.endTime);

    this.location = loc;
    this.daysOfWeek = dow;
    
} //Course_meeting()