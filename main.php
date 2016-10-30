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
    
    
    <div>
      <form action="handle.php" method="post">
        <textarea name="textArea" id="textArea_id" placeholder="Paste schedule here" ></textarea>
 
<!-- 
        <input type="radio" name="reminders" value="none" checked>None<br>
        <input type="radio" name="reminders" value="email">Email<br>
        <input type="radio" name="reminders" value="popup">Popup<br>
  -->
		<br>
        <input type="submit">
      </form>

    </div>

    
    <pre id="output"></pre>

    

    
  </body>
  </html>
