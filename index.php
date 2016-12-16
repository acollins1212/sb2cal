<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="mainstyle.css">
  
  <script type="text/javascript" src="scheduler.js"></script>
  </head>

  <body>
	<div id="main-container">
    <h1>Schedule Builder to Google Calendar</h1>
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
    <div id="form">
      <form action="handle.php" method="post">

        <div><input name="CALENDAR_ID" id="calendar-id" type="text"  placeholder="xxxxxxxxxx@group.calendar.google.com">
        </div>
        <textarea name="textArea" id="textArea-id" placeholder="Paste schedule here" ></textarea>

    		<br>
        <input type="submit" value="Submit">

      </form>

    </div>

    
    <pre id="output"></pre>
	<br>
	<p id="footer"> sb2cal.com </p>

    

	</div>
  </body>
  </html>
