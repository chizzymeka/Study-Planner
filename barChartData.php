<?php

include('session.php');
?>

<?php

//set header to json
header('Content-Type: application/json');

// Call the JSON generator function and pass the session variable, which contains the week number specifier that the SQl query will work with.
barChartDataProvider($_SESSION['count']);

// JSON generator which provides the data for plotting the barchart
function barChartDataProvider($weekNumber) {
    // Establish connection to the server
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "p00702";
    // Create connection
    $mysqli = new mysqli($servername, $username, $password, $dbname);

    //turn off autocommit
    $mysqli->autocommit(false);

    // Set up a flag for monitoring the individual queries
    $flag = true;

    // User ID initialisation
    $userid = $_SESSION['user_id'];
    
    //Obtain the schedule for the current week, get the dates, total number of hours per date, module codes and module titles
    $weeklyScheduleQuery = "SELECT studydate, SUM(numberofstudyhours) AS numberofstudyhours FROM studyplans LEFT JOIN `modulecodes` ON `studyplans`.`modulecodeid` = `modulecodes`.`modulecodeid` LEFT JOIN `users` ON `modulecodes`.`userid` = `users`.`userid` WHERE WEEK(studydate) = (WEEK(CURRENT_DATE()) + $weekNumber) AND `users`.`userid` = $userid GROUP BY studydate ORDER BY studydate ASC;";
    // Execute the query and put the result in the variable $weeklyScheduleResult
    $weeklyScheduleResult = mysqli_query($mysqli, $weeklyScheduleQuery);

    $data = array();

    // Fetch the result of the query as an array.
    while ($weeklyScheduleQueryResult = mysqli_fetch_array($weeklyScheduleResult, MYSQLI_ASSOC)) {
        //Put the result in array variables, the 'data' will tag every array in each variable with the tag 'data'
        $data[] = $weeklyScheduleQueryResult;
    }

    echo json_encode($data);

    // Finally, close the connection
    mysqli_close($mysqli);
}
