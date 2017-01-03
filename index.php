<!DOCTYPE html>
<html lang="en">
  <head>
  	<title>sb2cal - Home</title>
	<link rel="stylesheet" type="text/css" href="style.css">

	<!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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

  <body>
	<div id="main-container">
    <h1>Schedule Builder to Google Calendar</h1>
    <h3>Works for UC Davis Winter Quarter 2017</h3>
	<div id="instruction-div">
		<p><strong>Instructions</strong></p>
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
					<li>From there, you can export the calendar to any other type of calendar</li>
					<li>Look up "how to import ics files" for your device to do this</li>
				</ol>
		</ol>
		<p>If you have issues, follow along with this <strong>video walkthrough</strong>: <a href="https://youtu.be/jqCcCRws8Z8">https://youtu.be/jqCcCRws8Z8</a></p>
	</div>
    <div>
      <form name="form" action="handle.php" method="post" onsubmit="return(fixID())">

        <div><input name="CALENDAR_ID" id="calendar-id" type="text"  placeholder="xxxxxxxxxxxxxxx@group.calendar.google.com" required>
        </div>
        <textarea name="textArea" id="textArea-id" placeholder="Paste schedule here" required ></textarea>

    	<br>
        <input id="submit-schedule" type="submit" value="Submit">

      </form>

    </div>

    
    <pre id="output"></pre>
	<br>
	<br>
	<br>
	<footer> <a href="about.html">About sb2cal.com</a> </footer>


    

	</div>
  </body>
  </html>
