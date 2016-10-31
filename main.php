<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="./mainstyle.css">
  
  <script type="text/javascript" src="scheduler.js"></script>
  <script type="text/javascript" src="ID+scope.js">
    //NOT UPLOADED TO GITHUB FOR PRIVACY REASONS 
    //contains vital variables: CLIENT_ID, SCOPES, CALENDAR_ID
  </script> 
  <script src="https://apis.google.com/js/client.js?onload=checkAuth"></script>
  </head>

  <body>
    <h1>Schedule Builder to Google Calendar</h1>
	<h3>Note: This only works on Google Chrome at the moment!</h3>
    <p>Instructions</p>
	<ol>
		<li>Create a new calendar in Google Calendar. Take note of its calendar id (describe how to do this).</li>
		<li>Go to <a href="https://my.ucdavis.edu/schedulebuilder/index.cfm?sb" target="_blank">Schedule Builder</a></li>
		<li>Click "Show (Important) Details" for every single course in your schedule</li>
		<li>Highlight all of your schedules on the page. Make sure you're using Chrome!</li>
		<li>Paste your schedules into the text box below</li>
		<li>Click Submit. The next page will give you a chance to edit any of your schedule entries</li>
		<li>Paste your calendar id on the next page as well</li>
		<li>Insert into Google Calendar!</li>
	</ol>
    <div>
      <form action="handle.php" method="post">
        <textarea name="textArea" id="textArea_id" placeholder="Paste schedule here" ></textarea>
 
<!-- 
        <input type="radio" name="reminders" value="none" checked>None<br>
        <input type="radio" name="reminders" value="email">Email<br>
        <input type="radio" name="reminders" value="popup">Popup<br>
  -->
		<br>
        <input type="submit" value="Submit">
      </form>

    </div>

    
    <pre id="output"></pre>

    

    
  </body>
  </html>
