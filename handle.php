<html>
<head>

  <link rel="stylesheet" type="text/css" href="./mainstyle.css">
  <script type="text/javascript" src="scheduler.js"></script>
  <?php require 'classes.php'; ?>

</head>
<body>

<!--Google Calendar API Functions -->
<script type="text/javascript">
 
    var CLIENT_ID = '939118948007-sdlatljv3k8rpir0m4anb2ub73i9sr6a.apps.googleusercontent.com';
    var SCOPES = ["https://www.googleapis.com/auth/calendar.readonly"];

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
        var textContent = document.createTextNode(message + '\n');
        pre.appendChild(textContent);
    }

</script>

<script src="https://apis.google.com/js/client.js?onload=checkAuth"></script>

<?php

function parseSchedule() {

    $fullSchedule = $_POST["textArea"];
    

    $courseName_pattern = '/[A-Z]{3} [0-9]{3}[A-Z]{0,1} [A-Z0-9]{3} - .*/';
    $courseName_array;

    preg_match_all($courseName_pattern, $fullSchedule, $courseName_array);


    $uniqueDelimiter = "!@#!@#!@#!@#!";
    $uniqueDelimiter_pattern = '/' . $uniqueDelimiter . '/m';
    $numCourses = count($courseName_array[0]);

    for ($i = 0; $i < $numCourses; $i++) {
        $currentCourse = $courseName_array[0][$i];
        $currentCourse_pattern = '/' . $currentCourse . '/m';

        $fullSchedule = preg_replace($currentCourse_pattern, 
                                     $uniqueDelimiter . $currentCourse, 
                                     $fullSchedule);

        $scheduleArray = preg_split($uniqueDelimiter_pattern, $fullSchedule, 
                                    NULL, PREG_SPLIT_NO_EMPTY);

    } //for

    return $scheduleArray;

} //parseSchedule()

function printHeader($currentCourse, $i) {
    
    echo '<tr><th><input type="text" id="course' . $i 
        . '" value="' . $currentCourse->courseCode . '"></th>' 
        . '<th><input type="text" id="desc' . $i 
        . '" value="' . $currentCourse->descriptionStr . '"></th>'
        . '<th><input type="text" id="' . $i . 'section'
        . '" value="' . $currentCourse->section . '"></th></tr>';
    echo '<tr><td>Final Exam: </td>' 
        . '<td><input type="text" id="finalDate' . $i 
        . '" value="' . $currentCourse->finalDate . '">'
        . '<td><input type="text" id="finalTime' . $i 
        . '" value="' . $currentCourse->finalTime . '"></td>'
        . '</tr></thead>';
        

    echo '<th><input type="hidden" id="' . $i . 'numMeetings"' 
        . ' value="' . $currentCourse->numMeetings . '" ></th>';


} //echoHeader()

function printMeeting( $currentMeeting, $i, $j) {

    echo '<tr>' 
        . '<td><input type="text" id="' . $i . 'meetingType' . $j 
        . '" value="' . $currentMeeting->meetingType . '"></td>'
        . '<td><input type="text" id="' . $i 
        . 'startTime' . $j . '" value="' . $currentMeeting->startTime . '"></td>'
        . '<td><input type="text" id="' . $i 
        . 'endTime' . $j . '" value="' . $currentMeeting->endTime . '"></td>'
        . '</tr>'
        . '<tr>' . '<td>@</td>'
        . '<td><input type="text" id="' . $i 
        . 'location' . $j . '" value="' . $currentMeeting->location . '"></td>'
        . '<td><input type="text" id="' . $i 
        . 'daysOfWeek' . $j . '" value="' . $currentMeeting->daysOfWeek . '"></td>'
        . '</tr>';

    

} //echoMeeting()


//Set up Course objects to be used
$courseList = parseSchedule();
$courseArray = [];

//For some reason, Internet Explorer copy-paste puts in null elements
//Had something to do with a null character
$numCourses = count($courseList);
for($i = 0; $i < $numCourses; $i++) {
    if(strlen($courseList[$i]) < 25) {
         array_splice($courseList, $i, 1);
         $numCourses--;
         $i--;
     } //if

} //for

//courseArray inputted  all Course objects
for ($i = 0; $i < $numCourses; $i++) {
    $courseArray[] = new Course($courseList[$i]);

} //for

?>

<script type="text/javascript">

    var CALENDAR_ID = '<?php echo $_POST["CALENDAR_ID"]; ?>'; 
    CALENDAR_ID = CALENDAR_ID.trim();

    function insertAllCourses() {
        COURSE_ARRAY = [];
        var numCourses = <?php echo $numCourses; ?>;

        for (var i = 0; i < numCourses; i++) {
            var currentCourse = getCourse(i);


            for(var j = 0; j < getCourse(i).meetingArray.length; j++) {


                var event = currentCourse.getEventJSON(j);

                var request = gapi.client.calendar.events.insert({
                    'calendarId': CALENDAR_ID,
                    'resource': event
                });

                request.execute(function(event) {
              //appendPre('Event created: ' +   event.htmlLink);

            });

          } //inner for

          //insert the Final Exam event
          var finalExam = currentCourse.getFinalJSON();
          var request = gapi.client.calendar.events.insert({
              'calendarId': CALENDAR_ID,
              'resource': finalExam
          });

          request.execute(function(finalExam) {
            //appendPre('Final created: ' +   finalExam.htmlLink);

          });
        } //outer for
            
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

</script>


<div id="main-container">
    <div id="header">
    <h1 >Your Schedule</h1>
    <h4>Please double check that these were read correctly before inserting them into your Google Calendar</h4>
    </div>

<div id="authorize-div" style="display: none">
      <span>Please authorize access to SB2CAL to add to the calendar you selected before submitting!</span>
      <!--Button for the user to click to initiate auth sequence -->
      <button id="authorize-button" onclick="handleAuthClick(event)">
        Authorize
      </button>
</div>
<?php

    //Double-check with user. Is everything inserted correctly??
    for ($i = 0; $i < Course::$numCourses; $i++) {
        $currentCourse = $courseArray[$i];
        if ($i % 2 == 0) {
            echo '<div class="two-tables">';
            echo '<table class="left-course-table">';

        } 
        else {
            echo '<table class="right-course-table">';
        }

        echo '<col style="width:20%"> <col style="width:40%">'
            .'<col style="width:40%"><thead>';

        //create table header row for current course
        printHeader($currentCourse, $i);

        //output all the meetings
        for ($j = 0; $j < $currentCourse->numMeetings; $j++) {
            $currentMeeting = $currentCourse->meetingArray[$j];
            printMeeting( $currentMeeting, $i, $j);            
        } //inner for

        echo '</table>';
        if ($i % 2 == 1) {
            echo '</div>';
        } 
        
    } //outer for

    if ($i % 2 == 1) {
        echo '</div>';
    }

    echo '<input type="hidden" id="numCourses" value="' . $numCourses . '" >';

?>

<div style="clear: both; text-align: center; margin-top: 10px;">
<button id="loadCalendarApi-button" onclick="loadCalendarApi()">Insert Events</button>
</div>
<br>
<br>
<p id="footer" > sb2cal.com </p>

</div> <!-- Close main-container-->

<pre id="output"></pre>

</body>
</html>
