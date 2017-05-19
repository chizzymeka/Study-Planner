<?php
include('session.php');
?>

<!--Add Module Database Call-->
<?php
// Check whether form has been submitted
if (isset($_POST['addModuleDetailsSubmitted'])) {

    // Establish connection to the server
    include 'connectToServer.php';

    // Obtain data from the form and put it in corresponding variables
    $moduleCode = $_POST['moduleCode'];
    $moduleTitle = $_POST['moduleTitle'];

    //turn off autocommit
    $mysqli->autocommit(false);

    // Set up a flag for monitoring the individual queries
    $flag = true;

    // Initialise the first query
    $query1 = "INSERT INTO modulecodes (userid, modulecode) VALUES ('$userid', '$moduleCode');";
    // Execute the first query and put the result in the variable $result1
    $result1 = mysqli_query($mysqli, $query1);
    // If the first query fails, then set the flag to false and notify about the failure
    if (!$result1) {
        $flag = false;
        //echo "Error details for Result 1: " . mysqli_error($mysqli) . ".";
    }
    // Obtain the ID of the first insert operation
    $id = $mysqli->insert_id;

    // Initialise the second query, specifying the ID obtained above as the foreign key to be used in the second query
    $query2 = "INSERT INTO moduletitles (modulecodeid, moduletitle) VALUES ('$id', '$moduleTitle');";
    // Execute the second query and put the result in the variable $result2
    $result2 = mysqli_query($mysqli, $query2);
    // If the second query fails, then set the flag to false and notify about the failure
    if (!$result2) {
        $flag = false;
        //echo "Error details for Result 2: " . mysqli_error($mysqli) . ".";
    }

    // If the value of the flag remains 'true' after going through all the insertion attempts...
    if ($flag) {
        // then commit the transaction to the database.
        mysqli_commit($mysqli);
        //echo "All queries were executed successfully";
        // Else, cancel the transaction
    } else {
        mysqli_rollback($mysqli);
        //echo "All queries were rolled back";
    }

// Finally, close the connection
    mysqli_close($mysqli);
}
?>

<!--Add Study Plan-->
<?php
// Check whether form has been submitted
if (isset($_POST['studyPlanSubmitted'])) {
    // This post variable comes from the selected value of the dynamically generated dropdown of modules
    $moduleCodeId = $_POST['registeredModules'];
    // Obtain data from the form and put it in corresponding variables
    $moduleStudyDate = $_POST['moduleStudyDate'];
    $moduleAllocatedHours = $_POST['moduleAllocatedHours'];
    $studyPlanComments = $_POST['studyPlanComments'];

    // Establish connection to the server
    include 'connectToServer.php';

    //turn off autocommit
    $mysqli->autocommit(false);

    // Set up a flag for monitoring the individual queries
    $flag = true;

    // ALGORITHM 1: Check for date match
    $dateMatchQuery = "SELECT COUNT(`studyplans`.`studydate`) AS numberofexistingstudydates FROM `studyplans` LEFT JOIN `modulecodes` ON `studyplans`.`modulecodeid` = `modulecodes`.`modulecodeid` LEFT JOIN `users` ON `modulecodes`.`userid` = `users`.`userid` WHERE studydate = '$moduleStudyDate' AND `users`.`userid` = $userid;";
    // Execute the query and put the result in the variable $dateMatchQueryResult
    $dateMatchResult = mysqli_query($mysqli, $dateMatchQuery);
    // Fetch the result of the query as an array.
    $dateMatchQueryResult = mysqli_fetch_assoc($dateMatchResult);
    // Specify the array index and put the content in a variable
    $dateMatches = $dateMatchQueryResult['numberofexistingstudydates'];
    // If the first query fails, then set the flag to false and notify about the failure
    if (!$dateMatchResult) {
        $flag = false;
        //echo "Error details for Result 1: " . mysqli_error($mysqli) . ".";
    }

    // Check the total number of allocated hours on that day
    $totalNumberOfAllocatedHoursInaDayQuery = "SELECT `studyplans`.`studydate`, COALESCE(SUM(`studyplans`.`numberofstudyhours`), 0) AS totalnumberofstudyhours FROM `studyplans` LEFT JOIN `modulecodes` ON `studyplans`.`modulecodeid` = `modulecodes`.`modulecodeid` LEFT JOIN `users` ON `modulecodes`.`userid` = `users`.`userid` WHERE studydate = '$moduleStudyDate' AND `users`.`userid` = $userid;";
    // Execute the query and put the result in the variable $dateMatchQueryResult
    $totalNumberOfAllocatedHoursInaDayResult = mysqli_query($mysqli, $totalNumberOfAllocatedHoursInaDayQuery);
    // Fetch the result of the query as an array.
    $totalNumberOfAllocatedHoursInaDayQueryResult = mysqli_fetch_assoc($totalNumberOfAllocatedHoursInaDayResult);
    // Specify the array index and put it in a variable as a string
    $currentNumberOfAllocatedHoursInADay = $totalNumberOfAllocatedHoursInaDayQueryResult ['totalnumberofstudyhours'];
    // If the first query fails, then set the flag to false and notify about the failure
    if (!$totalNumberOfAllocatedHoursInaDayQueryResult) {
        $flag = false;
        //echo "Error details for Result 1: " . mysqli_error($mysqli) . ".";
    }

    // ALGORITHM 2: Continue on to check for hours of work allocated for subject week
    $weekNumberMatchQuery = "SELECT COALESCE(SUM(numberofstudyhours), 0) AS 'totalNumberOfAllocatedHoursInAWeekNumber' FROM studyplans LEFT JOIN `modulecodes` ON `studyplans`.`modulecodeid` = `modulecodes`.`modulecodeid` LEFT JOIN `users` ON `modulecodes`.`userid` = `users`.`userid` WHERE WEEK(studydate) = WEEK('$moduleStudyDate') AND `users`.`userid` = $userid;";
    // Execute the  query and put the result in the variable $weekNumberMatchResult
    $weekNumberMatchResult = mysqli_query($mysqli, $weekNumberMatchQuery);
    // Fetch the result of the query as an array.
    $weekNumberMatchQueryResult = mysqli_fetch_assoc($weekNumberMatchResult);
    // Specify the array index and put it in a variable as a string
    $currentlyAllocatedHoursForTheWeek = $weekNumberMatchQueryResult['totalNumberOfAllocatedHoursInAWeekNumber'];
    // If the query fails, then set the flag to false and notify about the failure
    if (!$weekNumberMatchResult) {
        $flag = false;
        //echo "Error details for Result 1: " . mysqli_error($mysqli) . ".";
    }

    // ALGORITHM 3: Continue other checks for total number of study days allowed in a week
    $totalNumberOfDaysAllowedInaWeekQuery = "SELECT COUNT(DISTINCT studydate) AS currentlyplanneddays FROM `studyplans` LEFT JOIN `modulecodes` ON `studyplans`.`modulecodeid` = `modulecodes`.`modulecodeid` LEFT JOIN `users` ON `modulecodes`.`userid` = `users`.`userid` WHERE WEEK(studydate) = WEEK('$moduleStudyDate') AND `users`.`userid` = $userid;";
    // Execute the query and put the result in the variable $totalNumberOfDaysAllowedInaWeekResult
    $totalNumberOfDaysAllowedInaWeekResult = mysqli_query($mysqli, $totalNumberOfDaysAllowedInaWeekQuery);
    // Fetch the result of the query as an array.
    $totalNumberOfDaysAllowedInaWeekQueryResult = mysqli_fetch_assoc($totalNumberOfDaysAllowedInaWeekResult);
    // Specify the array index and put it in a variable as a string
    $totalNumberOfDaysAllowedInAWeek = $totalNumberOfDaysAllowedInaWeekQueryResult['currentlyplanneddays'];
    // If the first query fails, then set the flag to false and notify about the failure
    if (!$totalNumberOfDaysAllowedInaWeekResult) {
        $flag = false;
        //echo "Error details for Result 1: " . mysqli_error($mysqli) . ".";
    }

    //... check if hours allocated for studies on that day plus the planned number of study hours is above 12...
    if (($currentNumberOfAllocatedHoursInADay + $moduleAllocatedHours) > 12) {
        //...if it is, then warn user
        echo "<script>";
        echo "alert('NOT ALLOWED: You will exceed the allowed 12 hours per day limit.');";
        echo "</script>";
        //This line refreshes the page after 0 seconds to stop the blank page caused by the return statement and also to help load data into bar chart
        echo '<META HTTP-EQUIV="Refresh" Content="0; URL=welcome.php">';
        return;
    }

    //... if it is, then check if the allocation plus the planned number of study hours is above 44
    if (($currentlyAllocatedHoursForTheWeek + $moduleAllocatedHours) > 44) {
        //... if it is, then warn user
        echo "<script>";
        echo "alert('NOT ALLOWED: You will exceed the allowed 44 hours per week limit.');";
        echo "</script>";
        //This line refreshes the page after 0 seconds to stop the blank page caused by the return statement and also to help load data into bar chart
        echo '<META HTTP-EQUIV="Refresh" Content="0; URL=welcome.php">';
        return;
    }

    // If the already booked days in the subject week is not than 6...
    if ((($totalNumberOfDaysAllowedInAWeek + 1) > 6)) {
        //...if it is, then warn user
        echo "<script>";
        echo "alert('NOT ALLOWED: You will exceed the allowed 6 days a week limit.');";
        echo "</script>";
        //This line refreshes the page after 0 seconds to stop the blank page caused by the return statement and also to help load data into bar chart
        echo '<META HTTP-EQUIV="Refresh" Content="0; URL=welcome.php">';
        return;
    }

    // Proceed to save the study plan if all tests are passed
    // Initialise the first query, specifying the ID obtained above as the foreign key to be used in the first query
    $query1 = "INSERT INTO studyplans (modulecodeid, studydate, numberofstudyhours) VALUES ('$moduleCodeId', '$moduleStudyDate', '$moduleAllocatedHours');";
    // Execute the second query and put the result in the variable $result3
    $result1 = mysqli_query($mysqli, $query1);
    // If the third query fails, then set the flag to false and notify about the failure
    if (!$result1) {
        $flag = false;
        //echo "Error details for Result 1: " . mysqli_error($mysqli) . ".";
    }
    // Initialise the second query, specifying the ID obtained above as the foreign key to be used in the second query
    $query2 = "INSERT INTO comments (modulecodeid, comment) VALUES ('$moduleCodeId', '$studyPlanComments');";
    // Execute the second query and put the result in the variable $result4
    $result2 = mysqli_query($mysqli, $query2);
    // If the fourth query fails, then set the flag to false and notify about the failure
    if (!$result2) {
        $flag = false;
        //echo "Error details for Result 2: " . mysqli_error($mysqli) . ".";
    }
    // If the value of the flag remains 'true' after going through all the insertion attempts...
    if ($flag) {
        // then commit the transaction to the database.
        mysqli_commit($mysqli);
        //echo "All queries were executed successfully";
        // Else, cancel the transaction
    } else {
        mysqli_rollback($mysqli);
        //echo "All queries were rolled back";
    }
    // Finally, close the connection
    mysqli_close($mysqli);
}
?>

<!--Change Password Database Call-->
<?php
// Check whether form has been submitted
if (isset($_POST['passwordUpdated'])) {

    // Establish connection to the server
    include 'connectToServer.php';

    // Obtain data from the form and put it in corresponding variables
    $inputPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];

    //turn off autocommit
    $mysqli->autocommit(false);

    // Set up a flag for monitoring the individual queries
    $flag = true;

    // Initialise the first query
    $query1 = "SELECT password FROM logins WHERE userid = $userid;";
    // Execute the first query and put the result in the variable $result1
    $result1 = mysqli_query($mysqli, $query1);
    // Fetch the result of the query as an array.
    $passwordOnRecord = mysqli_fetch_assoc($result1);
    // Specify the array index and put it in a variable as a string
    $currentUserPassword = $passwordOnRecord['password'];

    // If the first query fails, then set the flag to false and notify about the failure
    if (!$result1) {
        $flag = false;
        //echo "Error details for Result 1: " . mysqli_error($mysqli) . ".";
    }

    // If provided current password is equal to current password and new password is equal to confirmation of new password, then update the user's password...
    if ($inputPassword == $currentUserPassword && $newPassword == $confirmNewPassword) {
        // Initialise the second query
        $query2 = "UPDATE logins SET password = '$newPassword' WHERE userid = $userid;";
        // Execute the second query and put the result in the variable $result
        $result2 = mysqli_query($mysqli, $query2);
        // If the second query fails, then set the flag to false and notify about the failure
        if (!$result2) {
            $flag = false;
            //echo "Error details for Result 2: " . mysqli_error($mysqli) . ".";
        }

        // If the value of the flag remains 'true' after going through all the insertion attempts...
        if ($flag) {
            // then commit the transaction to the database.
            mysqli_commit($mysqli);
            //echo "All queries were executed successfully";
            // Else, cancel the transaction
        } else {
            mysqli_rollback($mysqli);
            //echo "All queries were rolled back";
        }

        // Finally, close the connection
        mysqli_close($mysqli);
        // .... Warn them that the provided values are incorrect
    } else {
        echo "Incorrect inputs - Please ensure the values are correct";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <title>Study Planner | Welcome <?php echo $_SESSION['user_firstname']; ?>!</title>
        
        <!-- jQuery for calendar handling -->
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <link href="jquery-ui-1.12.1.custom/jquery-ui.css" rel="stylesheet">
        <script src="jquery-ui-1.12.1.custom/external/jquery/jquery.js"></script>
        <script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>
        <script>
            $(document).ready(function () {
                $("#moduleStudyDate").datepicker({dateFormat: 'yy-mm-dd', minDate: 0, maxDate: "+12w"});<!-- Sets date format and restricts the dates from current day to 12 weeks in the future -->
            });
        </script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Viewport Meta Tag. Used to fit web page on all devices -->
    </head>
    <body>
        <div id="container">
            <!-- Link to external JavaScript file -->
        <script src="javascript/validator.js"></script>
        <br>
                <!-- Log Out Link -->
                <table id="logout" align="right">
                    <tr>
                        <td><a href = "logout.php">Log Out</a></td>
                    </tr>
                </table><br>
                <div id="welcomeMessage">
                        <!-- Welcome Message -->
                        <h2>Welcome <?php echo $_SESSION['user_firstname']; ?>!</h2>
                </div>
                <table id="addModuleChangePasswordTable" align="center">
                    <tr>
                        <td><button onclick="document.getElementById('id02').style.display = 'block'">Add New Modules</button></td> <!-- Button to open the modal Add Module form -->
                        <td><button onclick="document.getElementById('id01').style.display = 'block'">Change Password</button></td> <!-- Button to open the modal Change Password form -->
                    </tr>
                </table>
                <hr>
                <!-- The Modal -->
                <div id="id01" class="modal">
                    <span onclick="document.getElementById('id01').style.display = 'none'" class="close" title="Close Modal">&times;</span>
                    <!-- Modal Content -->
                    <form class="modal-content animate" onsubmit="return changePasswordValidator()" action="" method="POST">
                        <!--According to YouTuber Ralph Philips, this makes sure a blank form cannot be submitted to the database-->
                        <input type="hidden" name="passwordUpdated" value="true"/>
                        <div class="container">
                            <label><b>Current Password:*</b></label>
                            <input type="password" id="currentPassword" name="currentPassword" placeholder="Current password" value=""/>
                            <label><b>New Password:*</b></label>
                            <input type="password" id="newPassword" name="newPassword" placeholder="New password" value=""/>
                            <label><b>Confirm New Password:*</b></label>
                            <input type="password" id="confirmNewPassword" name="confirmNewPassword" placeholder="Confirm password" value=""/>
                            <button type="submit" name="changePasswordSubmit">Update Password</button>
                            <div class="container" style="background-color:#f1f1f1">
                                <button type="button" onclick="document.getElementById('id01').style.display = 'none'" class="cancelbtn">Cancel</button>
                            </div>
                            <div id="mandatoryFields">
                                <h5>* Mandatory Fields</h5>
                            </div>
                        </div>
                    </form>
                </div>
                <script>
                    // Get the modal
                    var modal = document.getElementById('id01');
                    // When the user clicks anywhere outside of the modal, close it
                    window.onclick = function (event) {
                        if (event.target === modal) {
                            modal.style.display = "none";
                        }
                    };
                </script>
                <!-- Register Modules -->
                <!-- The Modal -->
                <div id="id02" class="modal">
                    <span onclick="document.getElementById('id02').style.display = 'none'" class="close" title="Close Modal">&times;</span>
                    <!-- Modal Content -->
                    <form class="modal-content animate" onsubmit="return addModuleValidator()" action="welcome.php" method="POST">
                        <!--According to YouTuber Ralph Philips, this makes sure a blank form cannot be submitted to the database-->
                        <input type="hidden" name="addModuleDetailsSubmitted" value="true"/>
                        <div class="container">
                            <label><b>Module Code:*</b></label>
                            <input type="text" id="moduleCode" name="moduleCode" placeholder="Module code..." value=""/>
                            <label><b>Module Title:*</b></label>
                            <input type="text" id="moduleTitle" name="moduleTitle" placeholder="Module title..." value=""/>
                            <button type="submit" name="SubmitModuleDetails">Save</button>
                            <div class="container" style="background-color:#f1f1f1">
                                <button type="button" onclick="document.getElementById('id02').style.display = 'none'" class="cancelbtn">Cancel</button>
                            </div>
                            <div id="mandatoryFields">
                                <h5>* Mandatory Fields</h5>
                            </div>
                        </div>
                    </form>
                </div>
                <script>
                    // Get the modal
                    var modal = document.getElementById('id02');
                    // When the user clicks anywhere outside of the modal, close it
                    window.onclick = function (event) {
                        if (event.target === modal) {
                            modal.style.display = "none";
                        }
                    };
                </script>
                    <!-- Bar chart -->
                    <div id="barChartContainer" align="center">
                        <script type="text/javascript" src="d3/d3.js"></script>
                        <script src="d3/labratrevenge.com_d3-tip_javascripts_d3.tip.v0.6.3.js"></script>
                            <script>
                                            // set the dimensions of the canvas
                                            var margin = {top: 20, right: 20, bottom: 70, left: 20},
                                                width = 300 - margin.left - margin.right,
                                                height = 300 - margin.top - margin.bottom;

                                            // set the ranges
                                            var x = d3.scale.ordinal().rangeRoundBands([0, width], .05);

                                            var y = d3.scale.linear().range([height, 0]);

                                            // define the axis
                                            var xAxis = d3.svg.axis()
                                                .scale(x)
                                                .orient("bottom");

                                            var yAxis = d3.svg.axis()
                                                .scale(y)
                                                .orient("left")
                                                .ticks(1);
                                                
                                            // define the tooltip
                                            var tip = d3.tip()
                                                .attr('class', 'd3-tip')
                                                // offest function to place the tooltip and the bottom
                                                .offset(function() {
                                                return [this.getBBox().height + height/5, 0];
                                                })
                                                .html(function(d) {
                                                  return "<strong>Study Date:</strong> <span style='color:red'>" + d.studydate + "</span> | <strong>Alloted Hour(s):</strong> <span style='color:red'>" + d.numberofstudyhours + "</span>";
                                                });

                                            // add the SVG element
                                            var svg = d3.select("#barChartContainer").append("svg")
                                                .attr("width", width + margin.left + margin.right)
                                                .attr("height", height + margin.top + margin.bottom)
                                                .append("g")
                                                .attr("transform", 
                                                      "translate(" + margin.left + "," + margin.top + ")");
                                                      
                                            // add the tool tip
                                            svg.call(tip);

                                            // load the data
                                            d3.json("barChartData.php", function(error, data) {

                                                data.forEach(function(d) {
                                                    d.studydate = d.studydate;
                                                    d.numberofstudyhours = +d.numberofstudyhours;
                                                });

                                              // scale the range of the data
                                              x.domain(data.map(function(d) { return d.studydate; }));
                                              y.domain([0, d3.max(data, function(d) { return d.numberofstudyhours; })]);

                                              // add axis
                                              svg.append("g")
                                                  .attr("class", "x axis")
                                                  .attr("transform", "translate(0," + height + ")")
                                                  .call(xAxis)
                                                .selectAll("text")
                                                  .style("text-anchor", "end")
                                                  .attr("dx", "-.8em")
                                                  .attr("dy", "-.55em")
                                                  .attr("transform", "rotate(-90)" );

                                              svg.append("g")
                                                  .attr("class", "y axis")
                                                  .call(yAxis);
                                                /*Commented out to disable the 'Y' axis label
                                                  .append("text")
                                                  .attr("transform", "rotate(-90)")
                                                  .attr("y", 5)
                                                  .attr("dy", ".71em")
                                                  .style("text-anchor", "end")
                                                  .text("Hours");*/

                                              // Add bar chart
                                              svg.selectAll("bar")
                                                  .data(data)
                                                .enter().append("rect")
                                                  .attr("class", "bar")
                                                  .attr("x", function(d) { return x(d.studydate); })
                                                  .attr("width", x.rangeBand())
                                                  .attr("y", function(d) { return y(d.numberofstudyhours); })
                                                  .attr("height", function(d) { return height - y(d.numberofstudyhours); })
                                                  // add tooltip events
                                                  .on('mouseover', tip.show)
                                                  .on('mouseout', tip.hide);
                                            });
                            </script>
                    </div>
                    <label><b>Change Bar Chart Weekly View</b></label>
                    <form name="viewWeeklyBarChart" action="" method="POST">
                        <!--According to YouTuber Ralph Philips, this makes sure a blank form cannot be submitted to the database-->
                        <input type="hidden" name="weeklyViewSubmitted" value="true"/>
                        <select name="weeklyView" id="weeklyView" onchange="this.form.submit()">
                            <option selected disabled>Weekly View</option>
                            <option value=0>Bar Chart for Current Week Slot</option>
                            <option value=1>Bar Chart for Week Slot 2</option>
                            <option value=2>Bar Chart for Week Slot 3</option>
                            <option value=3>Bar Chart for Week Slot 4</option>
                            <option value=4>Bar Chart for Week Slot 5</option>
                            <option value=5>Bar Chart for Week Slot 6</option>
                            <option value=6>Bar Chart for Week Slot 7</option>
                            <option value=7>Bar Chart for Week Slot 8</option>
                            <option value=8>Bar Chart for Week Slot 9</option>
                            <option value=9>Bar Chart for Week Slot 10</option>
                            <option value=10>Bar Chart for Week Slot 11</option>
                            <option value=11>Bar Chart for Week Slot 12</option>
                        </select>
                    </form>
                                                        <?php
                                                        // If user selects another week, then the if statement below will pass the value of the week number to the JSON processing file on barChartData.php, for it to be used in running the SQL query over there.
                                                        // Please note that the if statement below will re-initilaise the session variable '$_SESSION['count']', which was set to '0' in login.php to whatever value the user selects from the weekly view dropdown.
                                                        if (isset($_POST['weeklyViewSubmitted'])) {
                                                            //Week number scroller
                                                            $_SESSION['count'] = $_POST['weeklyView'];
                                                            /* Commented Out: If statement for testing the content of the session variable '$_SESSION['count']'.
                                                              if (!empty($_SESSION['count'])) {
                                                              echo $_SESSION['count'];
                                                              } else {
                                                              echo "Session not yet set.";
                                                              } */
                                                        }
                                                        ?>
                <hr>
                    <h3>Add Study Plan</h3>
                    <form name="moduleInformation" action="welcome.php" onsubmit="return studyPlanValidator()" method="POST">
                        <!--According to YouTuber Ralph Philips, this makes sure a blank form cannot be submitted to the database-->
                        <input type="hidden" name="studyPlanSubmitted" value="true"/>
                        <label><b>Study Day (YY-MM-DD):*</b></label>
                            <input id="moduleStudyDate" name="moduleStudyDate" type="text" />
                        <div id="addModule">
                            <br><label><b>Registered Modules:*</b></label><br>
                                                        <?php
                                                        // Establish connection to the database
                                                        include 'connectToServer.php';

                                                        // Dynamically display the modules registered by the current user in a dropdown list
                                                        $query = "SELECT `modulecodes`.`modulecodeid`, `modulecodes`.`modulecode`, `moduletitles`.`moduletitle` FROM `modulecodes` LEFT JOIN `moduletitles` ON `moduletitles`.`modulecodeid` = `modulecodes`.`modulecodeid` WHERE userid = $userid";
                                                        $result = mysqli_query($mysqli, $query);

                                                        echo "<select name='registeredModules' id=\"registeredModules\">";
                                                        while ($row = mysqli_fetch_array($result)) {
                                                            echo "<option value='" . $row['modulecodeid'] . "'>" . " " . $row['modulecode'] . " - " . $row['moduletitle'] . " " . "</option>";
                                                        }
                                                        echo "</select>";
                                                        ?><br><br>
                            <label><b>Number of Hours:*</b></label><br>
                                <select name="moduleAllocatedHours" id="moduleAllocatedHours">
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                    <option value="4">Four</option>
                                    <option value="5">Five</option>
                                    <option value="6">Six</option>
                                    <option value="7">Seven</option>
                                    <option value="8">Eight</option>
                                    <option value="9">Nine</option>
                                    <option value="10">Ten</option>
                                    <option value="11">Eleven</option>
                                    <option value="12">Twelve</option>
                                </select>
                            <!--For simplicity, within the coursework context, this text area below has been disabled. However, there’s a plan to explore it further in the future evolution of the application; hence its dedicated table in the database.-->
                            <div id="studyPlanComments" style="display:none;">
                                <textarea id="studyPlanComments" name="studyPlanComments" placeholder="Comments..." value="" rows="4" cols="20" maxlength="200"></textarea>
                            </div>
                            <button type="submit" name="addModuleInformationSubmit">Save</button>
                            <div id="mandatoryFields">
                                <h5>* Mandatory Fields</h5>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <table id="dashboard" align="center">
                        <tr>
                            <td><a href = "moduleMaintenance.php">View Modules</a></td>
                            <td><a href = "calendarMaintenance.php">View Calendar</a></td>
                        </tr>
                    </table>
                    <hr>
                    <div id="footerText">
                        <h6>Copyright &copy; 2017, Chizzy Meka.</h6>
                    </div>
            </div>
    </body>
</html>