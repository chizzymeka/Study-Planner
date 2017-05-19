<?php
// Check whether form has been submitted
if (isset($_POST['submitted'])) {

    // Establish connection to the server
    include 'connectToServer.php';

    // Obtain data from the form and put it in corresponding variables
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $studentId = $_POST['studentId'];
    $mobile = $_POST['mobile'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    //turn off autocommit
    $mysqli->autocommit(false);

    // Set up a flag for monitoring the individual queries
    $flag = true;

    // Initialise the first query
    $query1 = "INSERT INTO users (firstname, lastname) VALUES ('$firstName', '$lastName');";
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
    $query2 = "INSERT INTO universityreferences (userid, universityreference) VALUES ('$id', '$studentId');";
    // Execute the second query and put the result in the variable $result2
    $result2 = mysqli_query($mysqli, $query2);
    // If the second query fails, then set the flag to false and notify about the failure
    if (!$result2) {
        $flag = false;
        //echo "Error details for Result 2: " . mysqli_error($mysqli) . ".";
    }
    // Obtain the ID of the second insert operation
    $id2 = $mysqli->insert_id;

    // Initialise the third query, specifying the ID obtained above as the foreign key to be used in the third query
    $query3 = "INSERT INTO mobiles (userid, mobile) VALUES ('$id', '$mobile');";
    // Execute the second query and put the result in the variable $result3
    $result3 = mysqli_query($mysqli, $query3);
    // If the third query fails, then set the flag to false and notify about the failure
    if (!$result3) {
        $flag = false;
        //echo "Error details for Result 3: " . mysqli_error($mysqli) . ".";
    }
    // Obtain the ID of the third insert operation
    $id3 = $mysqli->insert_id;

    // Initialise the fourth query, specifying the ID obtained above as the foreign key to be used in the fourth query
    $query4 = "INSERT INTO logins (userid, username, password) VALUES ('$id', '$username', '$password');";
    // Execute the second query and put the result in the variable $result4
    $result4 = mysqli_query($mysqli, $query4);
    // If the fourth query fails, then set the flag to false and notify about the failure
    if (!$result4) {
        $flag = false;
        //echo "Error details for Result 4: " . mysqli_error($mysqli) . ".";
    }

    // If the value of the flag remains 'true' after going through all the insertion attempts...
    if ($flag) {
        // then commit the transaction to the database.
        mysqli_commit($mysqli);
        //echo "All queries were executed successfully";
        // Else, cancel the transaction
        // redirect user to home page
        header("Location: index.php");
        // Make sure that codes below do not execute upon redirection.
        exit;
    } else {
        mysqli_rollback($mysqli);
        //echo "All queries were rolled back";
    }

    // Finally, close the connection
    mysqli_close($mysqli);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <title>Registration</title>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Viewport Meta Tag. Used to fit web page on all devices -->
    </head>
    <body>
        <!-- Link to external Javascript file -->
        <script src="javascript/validator.js"></script>
        <div id="container">
            <div id="header">
                <div id="logoText">
                    <h1>Study Planner</h1>
                </div>
            </div>
            <!-- Registration Form -->
            <h3>Please fill out the form below</h3>
            <form name="Register" action="registration.php" onsubmit="return registrationValidator()" autocomplete="on" method="POST">
                <!--According to YouTuber Ralph Philips, this makes sure a blank form cannot be submitted to the database-->
                <input type="hidden" name="submitted" value="true"/>
                <div class="register">
                    <label><b>First Name:*</b></label>
                    <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" autofocus/>
                    <label><b>Last Name:*</b></label>
                    <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" />
                    <label><b>Student ID:*</b></label>
                    <input type="text" id="studentId" name="studentId" placeholder="Enter your university ID" />
                    <label><b>Mobile:</b></label>
                    <input type="text" id="mobile" name="mobile" placeholder="Enter your phone number" />
                    <label><b>Email Address (Username):*</b></label>
                    <input type="email" id="username" name="username" placeholder="Enter your email address" emailPattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" />
                    <label><b>Password:*</b></label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" />
                    <label><b>Confirm Password:*</b></label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password" />
                    <button type="submit">Register</button>
                </div><br><br>
                <hr>
                <div id="back">
                    <a href="index.php">Back</a>
                </div>
                <hr>
                <br>
                <div id="mandatoryFields">
                    <h5>* Mandatory Fields</h5>
                </div>
            </form>
            <div id="footerText">
                <h6>Copyright &copy; 2017, Chizzy Meka.</h6>
            </div>
        </div>
    </body>
</html>