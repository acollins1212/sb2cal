<html>
<head>
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

    for ($i = 0; $i < $numCourses; $i++) {
        
        

    } //for

?>

</body>
</html>