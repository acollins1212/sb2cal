<!DOCTYPE html>
<html lang="en">
  <head>
  	<title>sb2cal - Home</title>
  	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- commenting out so I can try and get Angular to simply work with just HTML
	<link rel="stylesheet" type="text/css" href="style.css">
	-->
	
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>


	<!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  
	<script type="text/javascript" src="scheduler.js"></script>
	<script type="text/javascript">

		function fixID() {
			var idInput = document.forms["form"]["CALENDAR_ID"].value;
			var invalidCharacters = /[^@.\d\w_]|Calendar ID:/gi; //the only characters allowed, in a complement set
			var scrubbedInput = idInput.replace(invalidCharacters, "");
			document.forms["form"]["CALENDAR_ID"].value = scrubbedInput;
			if (scrubbedInput.length > 100) {
				return false;
			}
			return true;
		}

	</script>
  </head>

  <body  style="background-color: #AAA" >
	<div class="container-fluid" id="main-container">
    <h1>Schedule Builder to Google Calendar</h1>
    <h3>Works for UC Davis Winter Quarter 2017</h3>
	<div id="instruction-div">
		<p style="text-decoration: underline;""><strong>Instructions</strong></p>
		<ol>
			<li>Create a new calendar in Google Calendar</li>
			<li> Copy and paste the calendar ID into the first text box below</li>
				<ol type="a">
					<li> You can find the ID by clicking the arrow next to the calendar name</li> 
					<li>Scroll down and look for "Calendar Settings" -> "Calendar Address"</li>
				</ol>
			<li>Go to <a href="https://my.ucdavis.edu/schedulebuilder/index.cfm?sb" target="_blank">Schedule Builder</a></li>
				<ol type="a">
					<li>Click "Show (Important) Details" for every single course in your schedule</li>
					<li>Highlight all of your schedules on the page. Right-click and copy that selection</li>
					<li>Paste your schedule into the second text box</li>
				</ol>
			
			<li>Click Submit. The next page will give you a chance to edit any of your schedule entries</li>
			<li>Insert Events into Google Calendar! 
				<ol type="a">
					<li>From there, you can export the calendar if you go to calendar settings again</li>
					<li>To import, look up "how to import ics files" for your device</li>
				</ol>
		</ol>
		<p>If you have issues, follow along with this <strong>video walkthrough</strong>: <a href="https://youtu.be/jqCcCRws8Z8" target="_blank">https://youtu.be/jqCcCRws8Z8</a></p>
	</div>
    <div ng-app="myApp" ng-controller="myCtrl">
      <form name="form" action="handle.php" method="post" onsubmit="return(fixID())">

        <div><input ng-model="cid_test" name="CALENDAR_ID" id="calendar-id" type="text" required>
        </div>
        <textarea name="textArea" id="textArea-id" placeholder="Paste schedule here" required ></textarea>

    	<br>
        <input class="btn btn-default" id="submit-schedule" type="submit" value="Submit">

      </form>
	  <p> ID = {{cid_test}} </p>

    </div>
	
	<script>
	var app = angular.module('myApp', []);
	app.controller('myCtrl', function($scope) {
		$scope.cid_test = "xxxxxxxxxxxxxxx@group.calendar.google.com@@@";
	});
	</script>

   	<br>
	<br>
	<br>
	<footer> <a href="about.html">About sb2cal.com</a> </footer>


    

	</div>
  </body>
  </html>
