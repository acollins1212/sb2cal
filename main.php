<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="mainstyle.css">
  
  <script type="text/javascript" src="scheduler.js"></script>
  <script src="https://apis.google.com/js/client.js?onload=checkAuth"></script>
  </head>

  <body>
	<div class="main-container">
    <h1>Schedule Builder to Google Calendar</h1>
	<div id="instruction-div">
		<p><strong>Instructions</strong></p>
		<ol>
			<li>Create a new calendar in Google Calendar.</li>
				<ul>
					<li> Take note of its Calendar Id</li>
					<li>"Calendar Settings" -> "Calendar Address"</li>
				</ul>
			<li>Go to <a href="https://my.ucdavis.edu/schedulebuilder/index.cfm?sb" target="_blank">Schedule Builder</a></li>
			<li>Click "Show (Important) Details" for every single course in your schedule</li>
			<li>Highlight all of your schedules on the page.</li>
			<li>Paste your schedule into the text box below</li>
			<li>Paste your calendar id in the second text box</li>
			<li>Click Submit. The next page will give you a chance to edit any of your schedule entries</li>
			<li>Insert into Google Calendar!</li>
		</ol>
	</div>
    <div id="form">
      <form action="handle.php" method="post">

        <div><input name="CALENDAR_ID" id="calendar-id" type="text"  placeholder="xxxxxxxxxx@group.calendar.google.com"></div>
        <textarea name="textArea" id="textArea-id" placeholder="Paste schedule here" ></textarea>

    		<br>
        <input type="submit" value="Submit">

      </form>

    </div>

    
    <pre id="output"></pre>
	<br><br><br>
	<p style="text-align: right"> sb2cal.com </p>

    

	</div>
  </body>
  </html>
