<?php
include('session.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <title>Calendar Dashboard</title>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Viewport Meta Tag. Used to fit web page on all devices -->
    </head>
    <body>
        <div id="container">
            <h3>Calendar Dashboard</h3>
            <hr>
            <table align="center">
                <tr>
                    <td><a href = "welcome.php">Back to Welcome Page</a></td>
                </tr>
            </table>
            <hr>
            <!-- Display Calendar -->
            <?php
            // Establish connection to the server
            include 'connectToServer.php';

            // Selecting Database
            $db = mysqli_select_db($mysqli, "p00702");

            /* Commented out to disable the Update Module Query and avoid flawing the business logic: // If the update button is pressed...
              if (isset($_POST['updateStudyPlan'])) {
              //... update value that has been input
              $updateStudyPlanQuery = "UPDATE studyplans SET studydate = '$_POST[studydate]', numberofstudyhours = '$_POST[numberofstudyhours]' WHERE studyplanid = '$_POST[hidden]';";
              mysqli_query($mysqli, $updateStudyPlanQuery);
              } */

            // If the delete button is pressed...
            if (isset($_POST['deleteStudyPlan'])) {
                // ...delete the row
                $deleteStudyPlanQuery = "DELETE FROM studyplans WHERE studyplanid = '$_POST[hidden]';";
                mysqli_query($mysqli, $deleteStudyPlanQuery);
            }

            //  Query for populating study plan table
            $query = "SELECT `studyplans`.`studyplanid`, `modulecodes`.`modulecode`, `moduletitles`.`moduletitle`, `studyplans`.`studydate`, `studyplans`.`numberofstudyhours` FROM `modulecodes` LEFT JOIN `moduletitles` ON `moduletitles`.`modulecodeid` = `modulecodes`.`modulecodeid` LEFT JOIN `studyplans` ON `studyplans`.`modulecodeid` = `modulecodes`.`modulecodeid` WHERE studydate IS NOT NULL AND numberofstudyhours IS NOT NULL AND userid = $userid ORDER BY studydate ASC";

            // Execute the query and put the result in the variable $result
            $result = mysqli_query($mysqli, $query);

            // Dynamic table
            echo "<div id = \"tableContainer\">";
            echo "<table id=\"table\" border=1>
        <tr>
        <th>Code</th>
        <th>Title</th>
        <th>Date</th>
        <th>Study Hours</th>
        <!--<th>Update</th>Commented out to disable the Update Module Heading-->
        <th>Delete</th>
        </tr>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<form action=calendarMaintenance.php method=POST>";
                echo "<tr>";
                echo "<td>" . $row['modulecode'] . " </td>";
                echo "<td>" . $row['moduletitle'] . " </td>";
                echo "<td>" . $row['studydate'] . " </td>";
                echo "<td>" . $row['numberofstudyhours'] . " </td>";
                /* echo "<td>" . "<input type=submit name=updateStudyPlan value=Update " . " </td>"; Commented out to disable the Update Module button */
                echo "<td>" . "<button type=submit name=deleteStudyPlan>Remove</button>" . "</td>";
                echo "<td style=\"display: none \">" . "<input type=hidden name=hidden value=\"" . $row['studyplanid'] . "\" ></input>" . " </td>";
                echo "</tr>";
                echo "</form>";
            }
            echo "</table>";
            echo "</div>";

            // Finally, close the connection
            mysqli_close($mysqli);
            ?>
            <hr>
            <table align="center">
                <tr>
                    <td><a href = "welcome.php">Back to Welcome Page</a></td>
                </tr>
            </table>
            <hr>
            <div id="footerText">
                <h6>Copyright &copy; 2017, Chizzy Meka.</h6>
            </div>
        </div>
    </body>
</html>