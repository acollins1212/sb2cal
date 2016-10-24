<!DOCTYPE html>
<html>
<head>
  <script type="text/javascript" src="scheduler.js"></script>
  <script type="text/javascript" src="ID+scope.js">
    //NOT UPLOADED FOR PRIVACY REASONS 
    //contains vital variables: CLIENT_ID, SCOPES, CALENDAR_ID
  </script> 
  <script type="text/javascript">
      // Your Client ID can be retrieved from your project in the Google
      // Developer Console, https://console.developers.google.com
      

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
          //loadCalendarApi();
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
       * Load Google Calendar client library. Insert Events from each course
       * once client library is loaded.
       */
      function loadCalendarApi() {
        gapi.client.load('calendar', 'v3', insertEvents);
      }

      /**
       * Print the summary and start datetime/date of the next ten events in
       * the authorized user's calendar. If no events are found an
       * appropriate message is printed.
       */
       function listUpcomingEvents() {
        var request = gapi.client.calendar.events.list({
          'calendarId': CALENDAR_ID,
          'timeMin': (new Date()).toISOString(),
          'showDeleted': false,
          'singleEvents': true,
          'maxResults': 10,
          'orderBy': 'startTime'
        });

        request.execute(function(resp) {
          var events = resp.items;
          appendPre('Upcoming events:');

          if (events.length > 0) {
            for (i = 0; i < events.length; i++) {
              var event = events[i];
              var when = event.start.dateTime;
              if (!when) {
                when = event.start.date;
              }
              appendPre(event.summary + ' (' + when + ')')
            }
          } else {
            appendPre('No upcoming events found.');
          }

        });
      }

      function insertEvents() {
        //parseSchedule() currently gets text from id="textArea_id"
        var course_array = parseSchedule();

        var event;
        var course;
        for(var i = 0; i < course_array.length; i++){
          course = new Course(course_array[i]);

          for(var j = 0; j < course.meeting_array.length; j++) {


            event = course.getEventJSON(j);
            //console.log(event);

            var request = gapi.client.calendar.events.insert({
            'calendarId': CALENDAR_ID,
            'resource': event
            });

            request.execute(function(event) {
              appendPre('Event created: ' +   event.htmlLink);

            });


          } //inner for

          //insert the Final Exam event
          var final_exam = course.getFinalJSON();

          var request = gapi.client.calendar.events.insert({
           'calendarId': CALENDAR_ID,
           'resource': final_exam
          });

          request.execute(function(final_exam) {
            appendPre('Final created: ' +   final_exam.htmlLink);

          });
        } //outer for




        
      } //insertEvents()



      /**
       * Append a pre element to the body containing the given message
       * as its text node.
       *
       * @param {string} message Text to be placed in pre element.
       */
       function appendPre(message) {
        var pre = document.getElementById('output');
        var textContent = document.createTextNode(message + '\n');
        pre.appendChild(textContent);
      }

    </script>
    <script src="https://apis.google.com/js/client.js?onload=checkAuth">
    </script>
  </head>
  <body>
    <h1>Schedule Builder to Google Calendar</h1>
    <div id="authorize-div" style="display: none">
      <span>Authorize access to Google Calendar API</span>
      <!--Button for the user to click to initiate auth sequence -->
      <button id="authorize-button" onclick="handleAuthClick(event)">
        Authorize
      </button>
    </div>
    
    <div>
      <form action="handle.php" method="post">
        <textarea name="textArea" id="textArea_id" placeholder="Paste schedule here" style="width: 500px; height: 100px;"></textarea>
 
<!-- 
        <input type="radio" name="reminders" value="none" checked>None<br>
        <input type="radio" name="reminders" value="email">Email<br>
        <input type="radio" name="reminders" value="popup">Popup<br>
  -->

        <input type="submit">
      </form>

    </div>

    <div><button id="loadCalendarApi-button" onclick="loadCalendarApi()">Insert Events</button></div>
    <pre id="output"></pre>

    

    
  </body>
  </html>
