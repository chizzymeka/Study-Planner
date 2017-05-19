<?php

// Start session
session_start();
// Variable to store error message
$error = "";
// If the login form (Note that the 'submit' refers to the 'name' attribute of the login form) has been submitted...
if (isset($_POST['submit'])) {
    // If username or password is not provided...
    if (empty($_POST['username']) || empty($_POST['password'])) {
        // ...tell user that login details are invalid.
        $error = "Please fill in both your username and your password";
        // Else...
    } else {
        // ...put the provided username and password in variables $username and $password, respectively
        $username = $_POST['username'];
        $password = $_POST['password'];
        // Establish connection to the server
        $mysqli = mysqli_connect("localhost", "root", "");
        // set up measures to counter potential MySQL injections
        $username = stripslashes($username);
        $password = stripslashes($password);
        $username = mysqli_real_escape_string($mysqli, $username);
        $password = mysqli_real_escape_string($mysqli, $password);
        // Select Database
        $db = mysqli_select_db($mysqli, "p00702");
        // SQL query to fetch information of registerd users and find user match. This query accesses first name and last name (under users table) and login (under logins table) for the user. Beware that becuase of the redesign regarding adding modules and study plans differently, adding more joins for all tables will restrict user from signing in
        $query = mysqli_query($mysqli, "SELECT `logins`.*, `users`.* FROM `users` LEFT JOIN `logins` ON `logins`.`userid` = `users`.`userid` WHERE password = '$password' AND username = '$username'");
        // Return the number of rows of the query result and put it in $rows variable
        $rows = mysqli_num_rows($query);
        // If rows are equal to one...
        if ($rows == 1) {
            // Return the current row of a result set as an object
            $row = mysqli_fetch_object($query);
            unset($_SESSION['error']);
            // Initialize session with the username of the user...
            $_SESSION['login_user'] = $username;
            // Access the userid property of the object
            $_SESSION['user_id'] = $row->userid;
            // Access the firstname property of the object
            $_SESSION['user_firstname'] = $row->firstname;
            // Session variable for the initial barchart setup
            $_SESSION['count'] = 0;
            // ...and redirect to the homepage.
            header("Location: welcome.php");
            // Make sure that codes below do not execute upon redirection.
            exit;
            // Else, 
        } else {
            // and tell user that the login credentials are invalid.
            $error = "Your username or password is invalid";
            $_SESSION['error'] = $error;
            // redirect user to the home page (index.php)
            header("Location: index.php");
        }
        // ...and close connection
        mysqli_close($mysqli);
    }
}