<html>
<head>
  <link rel="stylesheet" type="text/css" href="./mainstyle.css">

</head>
<body>

<table>
<?php
    foreach ($_POST as $key => $value) {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo $value;
        echo "</td>";
        echo "</tr>";
    }
?>
</table>

<?php

    $numCourses = $_POST["numCourses"];

    $courseArray = [][];

    for ($i = 0; $i < $numCourses; $i++) {

        $courseKey = 'course' . $i; //course i
        $descriptionKey = 'desc' . $i; //desc  i


        $meetingCountKey = "course" . $i . "meetingcount";
        $numMeetings = $_POST[$meetingCountKey];
        for ($j = 0; $j < $numMeetings; $j++) {

            $meetingTypeKey = $i . 'meetingType' . $j;
            $startTimeKey = $i . 'startTime' . $j;
            $endTimeKey = $i . 'endTime' . $j;
            $locationKey = $i . 'location' . $j;
            $daysOfWeekKey = $i . 'daysOfWeek' . $j;


        } //inner for

//NOTE TO SELF: I MAY NOT NEED THIS PAGE. I CAN JUST GRAB THESE VALUES
        //WITH JAVASCRIPT STRAIGHT FROM THE TEXTBOXES BY THEIR ID

    } //for

?>

</body>
</html>