<html>
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


class Meeting {

	public $meetingType;
	public $startTime;
	public $endTime;
	public $daysOfWeek;
	public $location;

	function __construct($singleSchedule) {
		$meetingType_pattern = '/[a-zA-Z]+/';
		$startTime_pattern = '/\d{1,2}:\d{2} [A|P]M/';
		$endTime_pattern = '/- (\d{1,2}:\d{2} [A|P]M)/';
		$daysOfWeek_pattern = '/([A|P]M)([MTWRFS]+)[A-Z]/';
		$location_pattern = '/[A|P]M[MTWRFS]+([A-Z].*)/';

		//Taking $meetingType out of $singleSchedule
		if (preg_match($meetingType_pattern, $singleSchedule, $this->meetingType)) {
			$this->meetingType = $this->meetingType[0];
		}
		else { 
			echo "Meeting Type Error!";
		}

		//Taking $startTime out of schedule
		if (preg_match($startTime_pattern, $singleSchedule, $this->startTime)) {
			$this->startTime = $this->startTime[0];
		}
		else {
			echo "Start Time Error!";
		}

		//Taking $endTime out of schedule passed in
		if (preg_match($endTime_pattern, $singleSchedule, $this->endTime)) {
			$this->endTime = $this->endTime[1];
		} 
		else {
			echo "End Time Error!";
		}

		//Taking daysOfWeek out of schedule
		if (preg_match($daysOfWeek_pattern, $singleSchedule, $this->daysOfWeek)) {

			$this->daysOfWeek = $this->daysOfWeek[2];

			// VVV RFC 5545 Compliance block VVV
			$this->daysOfWeek = preg_replace('/M/', 'MO,', $this->daysOfWeek);
			$this->daysOfWeek = preg_replace('/T/', 'TU,', $this->daysOfWeek);
			$this->daysOfWeek = preg_replace('/W/', 'WE,', $this->daysOfWeek);
			$this->daysOfWeek = preg_replace('/R/', 'TH,', $this->daysOfWeek);
			$this->daysOfWeek = preg_replace('/F/', 'FR,', $this->daysOfWeek);

			$this->daysOfWeek = rtrim($this->daysOfWeek, ','); //chop off last comma

		}
		else {
			echo "Days Of Week Error!";
		}

		//taking location out of schedule passed in
		if (preg_match($location_pattern, $singleSchedule, $this->location)) {
			$this->location = $this->location[1];
			//echo $this->location;
		}
		else {
			echo "Location Error!";
		}

	} //Meeting constructor

} //Meeting class


class Course {

	public $meetingArray;
	public $courseCode;
	public $section;
	public $descriptionStr;

	function __construct($classString){

		$fullName = [];

		$namePattern = '/(^[A-Z]{3} \d{3}[A-Z]{0,2}) (.{3}) - (.+)/m';
		$schedulePattern = '/^.*[A|P]M .*/m';
		$examPattern = '/.* (\d{1,2}\/\d{1,2}\/\d{4}) (\d{1,2}:\d{2} [A|P]M)/m';

		
		preg_match($namePattern, $classString, $fullName);

		$this->courseCode = $fullName[1];
		$this->section = $fullName[2];
		$this->descriptionStr = $fullName[3];

		preg_match_all($schedulePattern, $classString, $scheduleArray);


		//Insert each course meeting into meeting array
		$numMeetings = count($scheduleArray[0]);
		for ($i = 0; $i < $numMeetings; $i++) {
	
			$this->meetingArray[] = new Meeting($scheduleArray[0][$i]);
		} //for

		//TO-DO: Insert final exams

	} //Class constructor

} //Course class

function checkWithUser($c) {
	
	echo '<div><input type="text" name="' . $c->courseCode . '" value="' . $c->courseCode . '" /></div>';

} //checkWithUser


$courseList = parseSchedule();
$object = new Course($courseList[0]);

$courseArray = [];

//create array of every class
$numCourses = count($courseList);
for ($i = 0; $i < $numCourses; $i++) {
	$courseArray[] = new Course($courseList[$i]);

} //for


?>

<form action="handle2.php" method="post">
	<table>
	<tr>
		<th>Course</th>
		<th>Title</th>

	<?php
		//Double-check with user. Is everything inserted correctly



		for ($i = 0; $i < $numCourses; $i++) {
			$currentCourse = $courseArray[$i];
			echo '<tr><td><input type="text" name="course' . $i 
				 . '" value="' . $currentCourse->courseCode . '" size="8" ></td>'
				 . '<td><input type="text" name="desc' . $i 
				 . '" value="' . $currentCourse->descriptionStr . '" size="15" ></td></tr>';

		} //for


	?>

	</table>
	<input type="submit">

</form>



</body>
</html>