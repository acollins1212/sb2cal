<!DOCTYPE html>
<html>
  <head>
  	<title>sb2cal</title>
	<link rel="stylesheet" type="text/css" href="style.css">
  
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
				<ul>
					<li> Find the ID in "Calendar Settings" -> "Calendar Address"</li>
				</ul>
			<li>Go to <a href="https://my.ucdavis.edu/schedulebuilder/index.cfm?sb" target="_blank">Schedule Builder</a></li>
			<li>Click "Show (Important) Details" for every single course in your schedule</li>
			<li>Highlight all of your schedules on the page. Right-click and copy that selection</li>
			<li>Paste your schedule into the second text box</li>
			<li>Click Submit. The next page will give you a chance to edit any of your schedule entries</li>
			<li>You must click the Authorize button for this to work. It will have you sign into your Google account</li>
			<li>Insert Events into Google Calendar! 
				<ul>
					<li>From there, you can export the calendar to any other type of calendar</li>
				<ul>
		</ol>
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
