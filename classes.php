<?php
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
	public static $numCourses = 0;
	public $meetingArray;
	public $courseCode;
	public $section;
	public $descriptionStr;
	public $numMeetings;
	public $finalDate;
	public $finalTime;

	function __construct($classString){

		$fullName = [];
		$this->numMeetings = 0;
		Course::$numCourses++;

		$namePattern = '/(^[A-Z]{3} \d{3}[A-Z]{0,2}) (.{3}) - (.+)/m';
		$schedulePattern = '/^.*[A|P]M .*/m';
		$examPattern = '/.* (\d{1,2}\/\d{1,2}\/\d{4}) (\d{1,2}:\d{2} [A|P]M)/m';

		
		preg_match($namePattern, $classString, $fullName);

		$this->courseCode = $fullName[1];
		$this->section = $fullName[2];
		$this->descriptionStr = $fullName[3];

		preg_match_all($schedulePattern, $classString, $scheduleArray);

		preg_match($examPattern, $classString, $finalDateTime);

		$this->finalDate = $finalDateTime[1];
		$this->finalTime = $finalDateTime[2];

		//Insert each course meeting into meeting array
		$this->numMeetings = count($scheduleArray[0]);
		for ($i = 0; $i < $this->numMeetings; $i++) {
	
			$this->meetingArray[] = new Meeting($scheduleArray[0][$i]);

			
		} //for

		//TO-DO: Insert final exams

	} //Class constructor

} //Course class
?>