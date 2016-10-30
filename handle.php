<html>
<head>

  <link rel="stylesheet" type="text/css" href="./mainstyle.css">
  <script type="text/javascript" src="scheduler.js"></script>
  <script type="text/javascript" src="ID+scope.js"></script>
  <script type="text/javascript" src="api_call.js"></script>

<!--

ISSUE WITH LOADING THE API FOR THE FUNCTIONS THAT NEED THEM
http://stackoverflow.com/questions/31144874/gapi-is-not-defined-google-sign-in-issue-with-gapi-auth2-init

-->

<script src="https://apis.google.com/js/client.js?onload=checkAuth"></script>

  <?php require 'classes.php'; ?>

</head>
<body>

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

		$fullSchedule = preg_replace($currentCourse_pattern, $uniqueDelimiter . $currentCourse, $fullSchedule);

		$scheduleArray = preg_split($uniqueDelimiter_pattern, $fullSchedule, NULL, PREG_SPLIT_NO_EMPTY);

	} //for

	return $scheduleArray;

} //parseSchedule()

function printHeader($currentCourse, $i) {

	echo '<th><input type="text" id="course' . $i 
		. '" value="' . $currentCourse->courseCode . '" size="8" ></th>' 
		. '<th><input type="text" id="desc' . $i 
		. '" value="' . $currentCourse->descriptionStr . '" size="15"></th>'
		. '<th><input type="text" id="finalDate' . $i 
		. '" value="' . $currentCourse->finalDate . '"></th> <th> @ </th> '
		. '<th><input type="text" id="finalTime' . $i 
		. '" value="' . $currentCourse->finalTime . '"></th>'
		. '</th>'
		. '<th><input type="text" id="' . $i . 'section'
		. '" value="' . $currentCourse->section . '"></th>' ;

	echo '<th><input type="hidden" id="' . $i . 'numMeetings"' 
		. ' value="' . $currentCourse->numMeetings . '" ></th>';


} //echoHeader()

function printMeeting( $currentMeeting, $i, $j) {

	echo '<tr>' . '<td><input type="text" id="' . $i 
		. 'meetingType' . $j . '" value="' . $currentMeeting->meetingType .'"></td>'
		. '<td><input type="text" id="' . $i 
		. 'startTime' . $j . '" value="' . $currentMeeting->startTime . '" width="8"></td>'
		. '<td><input type="text" id="' . $i 
		. 'endTime' . $j . '" value="' . $currentMeeting->endTime . '" width="8"></td>'
		. '</tr>'
		. '<tr>' . '<td>@</td>'
		. '<td><input type="text" id="' . $i 
		. 'location' . $j . '" value="' . $currentMeeting->location . '"></td>'
		. '<td><input type="text" id="' . $i 
		. 'daysOfWeek' . $j . '" value="' . $currentMeeting->daysOfWeek . '"></td>'
		. '</tr>';

	echo '<tr><td>----------------------------------------</td></tr>';

} //echoMeeting()

?>

<h1>Courses and their meetings:</h1>

<?php

	$courseList = parseSchedule();
	$courseArray = [];

	//courseArray will have all Course objects
	$numCourses = count($courseList);
	for ($i = 0; $i < $numCourses; $i++) {
		$courseArray[] = new Course($courseList[$i]);

	} //for


	//Double-check with user. Is everything inserted correctly??
	for ($i = 0; $i < Course::$numCourses; $i++) {
		$currentCourse = $courseArray[$i];

		echo '<table>';

		//create table header row for current course
		printHeader($currentCourse, $i);

		//output all the meetings
		for ($j = 0; $j < $currentCourse->numMeetings; $j++) {

			$currentMeeting = $currentCourse->meetingArray[$j];
			printMeeting( $currentMeeting, $i, $j);
			

		} //inner for

		echo '</table>';
		echo '<tr>=========================================</tr>';
	} //outer for

	echo '<input type="hidden" id="numCourses" value="' . $numCourses . '" >';

?>


<script type="text/javascript">


	function insertAllCourses() {
		COURSE_ARRAY = [];
		var numCourses = <?php echo $numCourses; ?>;

		for (var i = 0; i < numCourses; i++) {
			var currentCourse = getCourse(i);


			for(var j = 0; j < getCourse(i).meeting_array.length; j++) {


	        var event = currentCourse.getEventJSON(j);
	        console.log(event);

	        var request = gapi.client.calendar.events.insert({
	        'calendarId': CALENDAR_ID,
	        'resource': event
	        });

	        request.execute(function(event) {
	          appendPre('Event created: ' +   event.htmlLink);

	        });

	      } //inner for

	      //insert the Final Exam event
	      var final_exam = currentCourse.getFinalJSON();

	      var request = gapi.client.calendar.events.insert({
	       'calendarId': CALENDAR_ID,
	       'resource': final_exam
	      });

	      request.execute(function(final_exam) {
	        appendPre('Final created: ' +   final_exam.htmlLink);

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
		var course_id = document.getElementById(courseKey).value;
		var section = document.getElementById(sectionKey).value;
		var desc = document.getElementById(descriptionKey).value;
		var finalDate = document.getElementById(finalDateKey).value;
   		var finalTime = document.getElementById(finalTimeKey).value;

   		c = new Course(course_id, section, desc, finalDate, finalTime);

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
	 * Load Google Calendar client library. Insert Events from each course
	 * once client library is loaded.
	 */
	function loadCalendarApi() {
	  	gapi.client.load('calendar', 'v3', insertAllCourses());
	}


</script>



<div>
<button id="loadCalendarApi-button" onclick="loadCalendarApi()">Insert Events</button>
</div>



<div id="authorize-div" style="display: none">
      <span>Authorize access to Google Calendar API</span>
      <!--Button for the user to click to initiate auth sequence -->
      <button id="authorize-button" onclick="handleAuthClick(event)">
        Authorize
      </button>
</div>





</body>
</html>