/**
A.J. Collins

CONTAINS:
    Google Calendar API boilerplate
    Calendar insert functions
    Event insert functions


**/

    var CLIENT_ID = '939118948007-sdlatljv3k8rpir0m4anb2ub73i9sr6a.apps.googleusercontent.com';
    var SCOPES = ["https://www.googleapis.com/auth/calendar"];
    var CALENDAR_ID = "28jrtdbechofa8tmb3f26a67t4@group.calendar.google.com"; //HARDCODED FOR TESTING. FIXME!

    function _insertCalendar() {
        var summary = document.getElementById("calendar-name").value;

        var body = {
            'summary': summary,
            'description': "Created by sb2cal.com",
            'timeZone': "America/Los_Angeles",
            'location': "Davis"
        }

        var args = {
            'path': 'https://www.googleapis.com/calendar/v3/calendars',
            'method': 'POST',
            'body': body
        }

        var request = gapi.client.request(args);
        request.then(function(response) {
            //CALENDAR_ID = response.result.id;
        },           function(reason)   {
            //rejected. Add functionality to deal with this
        });

    } //_insertCalendar() helper


    function insertCalendar() {
        gapi.client.load('calendar', 'v3', _insertCalendar);
    }

    function insertEvent(event) {
        //API Call to insert entire course
        gapi.client.load('calendar', 'v3', function(){
            var request = gapi.client.calendar.events.insert({
                'calendarId': CALENDAR_ID,
                'resource': event
            });

            request.execute(function(event) { 
            } );

        });
    } //insertEvent()

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
        //gapi.client.load('calendar', 'v3', insertCalendar);
    }

    function insertCourse() {
        gapi.client.load('calendar', 'v3', _insertCourse);
    }
