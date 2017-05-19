<?php
include('session.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <title>Modules Dashboard</title>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Viewport Meta Tag. Used to fit web page on all devices -->
    </head>
    <body>
        <div id="container">
            <h3>Module Dashboard</h3>
            <h4>Important: Any module deletion operation will delete all associated study plans.</h4>
            <hr>
            <table align="center">
                <tr>
                    <td><a href = "welcome.php">Back to Welcome Page</a></td>
                </tr>
            </table>
            <hr>
            <?php
            // Establish connection to the server
            include 'connectToServer.php';

            // Selecting Database
            $db = mysqli_select_db($mysqli, "p00702");

            // If the update button is pressed...
            if (isset($_POST['updateModule'])) {
                //... update value that has been input
                $updateStudyPlanQuery = "UPDATE modulecodes, moduletitles SET modulecodes.modulecode = '$_POST[modulecode]', moduletitles.moduletitle = '$_POST[moduletitle]' WHERE modulecodes.modulecodeid = '$_POST[moduleCodeIdHidden]' AND moduletitles.moduletitleid = '$_POST[moduleTitleIdHidden]';";
                mysqli_query($mysqli, $updateStudyPlanQuery);
            }


            // If the delete button is pressed...
            if (isset($_POST['deleteModule'])) {
                // ...delete the row
                $deleteModuleCodeQuery = array("modulecodes", "moduletitles");
                foreach ($deleteModuleCodeQuery as $table) {
                    $query = "DELETE FROM $table WHERE modulecodeid = '$_POST[moduleCodeIdHidden]';";
                    mysqli_query($mysqli, $query);
                }
            }

            //  Query for populating modules table
            $query = "SELECT modulecodes.modulecodeid, modulecodes.modulecode, moduletitles.moduletitleid, moduletitles.moduletitle, modulecodes.created FROM modulecodes LEFT JOIN moduletitles ON moduletitles.modulecodeid = modulecodes.modulecodeid WHERE modulecodes.modulecodeid = moduletitles.modulecodeid AND userid = $userid ORDER BY modulecode ASC;";

            // Execute the query and put the result in the variable $result
            $result = mysqli_query($mysqli, $query);

            // Dynamic table
            echo "<div id = \"tableContainer\">";
            echo "<table id=\"table\" border=1>
        <tr>
        <th>Code</th>
        <th>Title</th>
        <!--<th>Created On</th> Commented out to disable the 'Created On' column */-->
        <th>Update</th>
        <th>Delete</th>
        </tr>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<form action=moduleMaintenance.php method=POST>";
                echo "<tr>";
                echo "<td>" . "<input type=text name=modulecode value=\"" . $row['modulecode'] . "\" ></input>" . " </td>";
                echo "<td>" . "<input type=text name=moduletitle value=\"" . $row['moduletitle'] . "\" ></input>" . " </td>";
                /* echo "<td>" . $row['created'] . " </td>";Commented out to disable the 'Created On' column */
                echo "<td>" . "<button type=submit name=updateModule>Update</button> " . " </td>";
                echo "<td>" . "<button type=submit name=deleteModule>Remove</button> " . " </td>";
                echo "<td style=\"display: none \">" . "<input type=hidden name=moduleCodeIdHidden value=\"" . $row['modulecodeid'] . "\" ></input>" . " </td>";
                echo "<td style=\"display: none \">" . "<input type=hidden name=moduleTitleIdHidden value=\"" . $row['moduletitleid'] . "\" ></input>" . " </td>";
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
