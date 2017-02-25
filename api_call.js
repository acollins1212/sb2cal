

    var CLIENT_ID = '939118948007-sdlatljv3k8rpir0m4anb2ub73i9sr6a.apps.googleusercontent.com';
    var SCOPES = ["https://www.googleapis.com/auth/calendar"];

    function insertCalendar(str) {
        var request = gapi.client.calendar.insert({
            'summary': str
        });

        request.execute(function(event) { console.log("success!"); } );
    }

    function insertAllCourses(numCourses) {
        var CALENDAR_ID = document.getElementById("calendar-id").value; 



        //Get rid of submit button. You don't want people submitting more than once
        var submitDiv = document.getElementById('submit-div');
        submitDiv.style.display = 'none';

        for (var i = 0; i < numCourses; i++) {
            var currentCourse = getCourse(i);


            for(var j = 0; j < getCourse(i).meetingArray.length; j++) {


                var event = currentCourse.getEventJSON(j);

                var request = gapi.client.calendar.events.insert({
                    'calendarId': CALENDAR_ID,
                    'resource': event
                });

                request.execute(function(event) {
                    appendPre(event.summary + ' - inserted');
                });

            } //inner for

          //insert the Final Exam event. Should be skipped if no final exam
          var finalExam = currentCourse.getFinalJSON();

          if (finalExam != "-1") {
              var request = gapi.client.calendar.events.insert({
                  'calendarId': CALENDAR_ID,
                  'resource': finalExam
              });

              request.execute(function(finalExam) {
                  appendPre(finalExam.summary + ' - inserted');

              });
          } //if

        } //outer for
        appendPre('Hope you enjoyed using sb2cal =)\n');

    } //insertAllCourses()

    function getCourse(i) {

        //These are the ID's for course elements
        var courseKey = 'course' + i; //course i
        var sectionKey = i + 'section'; 
        var descriptionKey = 'desc' + i; //desc  i
        var finalDateKey = 'finalDate' + i;
        var finalTimeKey = 'finalTime' + i;

        //Using the ID's from above, get the values inputted
        var courseID = document.getElementById(courseKey).value;
        var section = document.getElementById(sectionKey).value;
        var desc = document.getElementById(descriptionKey).value;
        var finalDate = document.getElementById(finalDateKey).value;
           var finalTime = document.getElementById(finalTimeKey).value;

           c = new Course(courseID, section, desc, finalDate, finalTime);

           //Get number of meetings for this course
        var numMeetingsKey = i + "numMeetings";
        var numMeetings = document.getElementById(numMeetingsKey).value;

        for (var j = 0; j < numMeetings; j++) {
            //Get the meeting values out of the text boxes
            var meetingTypeKey = i + 'meetingType' + j;
            var startTimeKey = i + 'startTime' + j;
            var endTimeKey = i + 'endTime' + j;
            var locationKey = i + 'location' + j;
            var daysOfWeekKey = i + 'daysOfWeek' + j;
            var type = document.getElementById(meetingTypeKey).value;
            var start = document.getElementById(startTimeKey).value;
            var end = document.getElementById(endTimeKey).value;
            var loc = document.getElementById(locationKey).value;
            var dow = document.getElementById(daysOfWeekKey).value;

            c.addMeeting(type, start, end, loc, dow);
        } //inner for
        return c; //return the course created

    } //getCourse(i)


    /**
     * Check if current user has authorized this application.
    */
    function checkAuth() {
        gapi.auth.authorize(
        {
            'client_id': CLIENT_ID,
            'scope': SCOPES.join(' '),
            'immediate': true
        }, handleAuthResult);
    }

    /**
     * Handle response from authorization server.
     *
     * @param {Object} authResult Authorization result.
    */
    function handleAuthResult(authResult) {
        var authorizeDiv = document.getElementById('authorize-div');
        if (authResult && !authResult.error) {
          // Hide auth UI, then load client library.
          authorizeDiv.style.display = 'none';
        } else {
          // Show auth UI, allowing the user to initiate authorization by
          // clicking authorize button.
          authorizeDiv.style.display = 'inline';
        }
    }

    /**
     * Initiate auth flow in response to user clicking authorize button.
     *
     * @param {Event} event Button click event.
    */
    function handleAuthClick(event) {
        gapi.auth.authorize(
        {client_id: CLIENT_ID, scope: SCOPES, immediate: false},
             handleAuthResult);
        return false;
    }

    /**
     * Load Google Calendar client library. List upcoming events
     * once client library is loaded.
    */
    function loadCalendarApi() {
        gapi.client.load('calendar', 'v3', insertAllCourses);
    }


    /**
     * Append a pre element to the body containing the given message
     * as its text node.
     *
     * @param {string} message Text to be placed in pre element.
    */
    function appendPre(message) {
        var pre = document.getElementById('output');

        if(pre.style.display == "none")
            pre.style.display = "block";
        var textContent = document.createTextNode(message + '\n');
        pre.appendChild(textContent);
    }



